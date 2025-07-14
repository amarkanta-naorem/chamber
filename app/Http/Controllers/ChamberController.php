<?php

namespace App\Http\Controllers;

use App\Exports\ChambersExport;
use App\Models\Chamber;
use App\Models\TemperatureSummary;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ChamberController extends Controller
{
    public function processChambersData($unique_id, $startDate, $endDate, $isExport = false)
    {
        $chambers = Chamber::whereIn('sys_service_id', $unique_id)
            ->whereBetween('date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            ])
            ->get()
            ->sortBy('time');

        $formattedChambers = [];

        foreach ($unique_id as $service_id) {
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $entries = $chambers->where('sys_service_id', $service_id)
                    ->where('date', $currentDate->format('Y-m-d'));

                // Parse times using Carbon for accurate comparison
                $entriesWithTime = $entries->map(function ($entry) {
                    $entry->timeCarbon = Carbon::createFromFormat('H:i:s', $entry->time);
                    return $entry;
                });

                // Find the first morning entry (before 12:00)
                $morningEntry = $entriesWithTime->filter(function ($entry) {
                    return $entry->timeCarbon->lt(Carbon::createFromTime(12, 0, 0));
                })->sortBy('time')->first();

                // Find the last afternoon entry (12:00 or later)
                $afternoonEntry = $entriesWithTime->filter(function ($entry) {
                    return $entry->timeCarbon->gte(Carbon::createFromTime(12, 0, 0));
                })->sortBy('time')->last();

                // $morningEntry = $entries->first(function ($entry) {
                //     return $entry->time < '12:00:00';
                // });

                // $afternoonEntry = $entries->last(function ($entry) {
                //     return $entry->time >= '12:00:00';
                // });

                $missingMorning = !$morningEntry;
                $missingAfternoon = !$afternoonEntry;

                if ($missingMorning && $afternoonEntry) {
                    $morningEntry = clone $afternoonEntry;
                    $morningEntry->time = '10:00:00';
                    $morningEntry->gps_time = $currentDate->format('Y-m-d') . ' 10:00:00';
                    $morningEntry->timeCarbon = Carbon::createFromTime(10, 0, 0);
                    $afternoonEntry->timeCarbon = Carbon::createFromTime(17, 0, 0);
                }

                if ($missingAfternoon && $morningEntry) {
                    $afternoonEntry = clone $morningEntry;
                    $afternoonEntry->time = '17:00:00';
                    $afternoonEntry->gps_time = $currentDate->format('Y-m-d') . ' 17:00:00';
                }

                $row = [
                    'sys_service_id' => $service_id,
                    'first_row_date' => $morningEntry ? $morningEntry->date : $currentDate->format('Y-m-d'),
                    'first_row_time' => $morningEntry ? $morningEntry->time : 'N/A',
                    'first_row_gps_time' => $morningEntry ? ($morningEntry->gps_time ?: $currentDate->format('Y-m-d') . ' 10:00:00') : $currentDate->format('Y-m-d') . ' 10:00:00',
                    'first_row_tel_temperature' => $morningEntry ? ($morningEntry->tel_temperature == 0 ? 'NW' : $morningEntry->tel_temperature) : 'NW',
                    'second_row_date' => $afternoonEntry ? $afternoonEntry->date : $currentDate->format('Y-m-d'),
                    'second_row_time' => $afternoonEntry ? $afternoonEntry->time : 'N/A',
                    'second_row_gps_time' => $afternoonEntry ? ($afternoonEntry->gps_time ?: $currentDate->format('Y-m-d') . ' 17:00:00') : $currentDate->format('Y-m-d') . ' 17:00:00',
                    'second_row_tel_temperature' => $afternoonEntry ? ($afternoonEntry->tel_temperature == 0 ? 'NW' : $afternoonEntry->tel_temperature) : 'NW',
                    'message' => ''
                ];

                // if ($missingMorning) {
                //     $row['message'] .= 'Morning data is missing';
                // }

                // if ($missingAfternoon) {
                //     $row['message'] .= 'Afternoon data is missing';
                // }

                $messages = [];
                if ($missingMorning) {
                    $messages[] = 'Morning data is missing';
                }
                if ($missingAfternoon) {
                    $messages[] = 'Afternoon data is missing';
                }
                $row['message'] = implode(', ', $messages);

                // Temperature correction for first row (morning)
                if (($row['first_row_tel_temperature'] == 'NW' || (is_numeric($row['first_row_tel_temperature']) && $row['first_row_tel_temperature'] > 40)) && is_numeric($row['second_row_tel_temperature'])) {
                    $maxTempEntry = TemperatureSummary::where('sys_service_id', $service_id)
                        ->where('temp_date', $currentDate->format('Y-m-d'))
                        ->first();

                    if ($maxTempEntry) {
                        $row['first_row_tel_temperature'] = $maxTempEntry->min_temp;

                        // if (!$isExport) {
                        //     $telemetryData = DB::table('latest_telemetry')
                        //         ->join('temperature_summary', 'temperature_summary.sys_service_id', '=', 'latest_telemetry.sys_service_id')
                        //         ->where('temperature_summary.sys_service_id', $service_id)
                        //         ->where('temperature_summary.temp_date', $currentDate->format('Y-m-d'))
                        //         ->select('latest_telemetry.*', 'temperature_summary.*')
                        //         ->first();

                        //     if ($telemetryData) {
                        //         $startTime = Carbon::createFromFormat('H:i:s', '00:00:00');
                        //         for ($i = 0; $i < 720; $i++) {
                        //             $currentGpsTime = $startTime->copy()->addMinute($i);
                        //             DB::table('telemetry_new_chamber')->insert([
                        //                 'sys_service_id' => $telemetryData->sys_service_id,
                        //                 'sys_msg_type' => $telemetryData->sys_msg_type,
                        //                 'sys_proc_time' => $currentDate->format('Y-m-d') . ' ' . $currentGpsTime->format('H:i:s'),
                        //                 'sys_proc_host' => $telemetryData->sys_proc_host ?? null,
                        //                 'sys_asset_id' => $telemetryData->sys_asset_id ?? null,
                        //                 'sys_geofence_id' => $telemetryData->sys_geofence_id ?? null,
                        //                 'sys_device_id' => $telemetryData->sys_device_id ?? 0,
                        //                 'gps_date' => $currentDate->format('Y-m-d'),
                        //                 'gps_time' => $telemetryData->gps_time,
                        //                 'gps_latitude' => $telemetryData->gps_latitude ?? 0.0,
                        //                 'gps_longitude' => $telemetryData->gps_longitude ?? 0.0,
                        //                 'gps_orientation' => $telemetryData->gps_orientation ?? 0.0,
                        //                 'gps_speed' => $telemetryData->gps_speed ?? 0.0,
                        //                 'gps_fix' => $telemetryData->gps_fix ?? null,
                        //                 'geo_street' => $telemetryData->geo_street ?? null,
                        //                 'geo_town' => $telemetryData->geo_town ?? null,
                        //                 'geo_country' => $telemetryData->geo_country ?? null,
                        //                 'geo_postcode' => $telemetryData->geo_postcode ?? null,
                        //                 'jny_distance' => $telemetryData->jny_distance ?? null,
                        //                 'jny_duration' => $telemetryData->jny_duration ?? null,
                        //                 'jny_idle_time' => $telemetryData->jny_idle_time ?? null,
                        //                 'jny_status' => $telemetryData->jny_status ?? '0',
                        //                 'jny_leg_code' => $telemetryData->jny_leg_code ?? null,
                        //                 'jny_device_jny_id' => $telemetryData->jny_device_jny_id ?? null,
                        //                 'des_movement_id' => $telemetryData->des_movement_id ?? null,
                        //                 'des_vehicle_id' => $telemetryData->des_vehicle_id ?? null,
                        //                 'tel_state' => $telemetryData->tel_state ?? 0,
                        //                 'tel_ignition' => $telemetryData->tel_ignition ?? null,
                        //                 'tel_alarm' => $telemetryData->tel_alarm ?? null,
                        //                 'tel_panic' => $telemetryData->tel_panic ?? null,
                        //                 'tel_shield' => $telemetryData->tel_shield ?? null,
                        //                 'tel_theft_attempt' => $telemetryData->tel_theft_attempt ?? null,
                        //                 'tel_tamper' => $telemetryData->tel_tamper ?? null,
                        //                 'tel_ext_alarm' => $telemetryData->tel_ext_alarm ?? null,
                        //                 'tel_journey' => $telemetryData->tel_journey ?? null,
                        //                 'tel_journey_status' => $telemetryData->tel_journey_status ?? null,
                        //                 'tel_idle' => $telemetryData->tel_idle ?? null,
                        //                 'tel_ex_idle' => $telemetryData->tel_ex_idle ?? null,
                        //                 'tel_hours' => $telemetryData->tel_hours ?? null,
                        //                 'tel_input_0' => $telemetryData->tel_input_0 ?? null,
                        //                 'tel_input_1' => $telemetryData->tel_input_1 ?? null,
                        //                 'tel_input_2' => $telemetryData->tel_input_2 ?? null,
                        //                 'tel_input_3' => $telemetryData->tel_input_3 ?? null,
                        //                 'tel_temperature' => $maxTempEntry->min_temp,
                        //                 'tel_voltage' => $telemetryData->tel_voltage ?? 0.0,
                        //                 'main_powervoltage' => $telemetryData->main_powervoltage ?? 0.0,
                        //                 'tel_odometer' => $telemetryData->tel_odometer ?? null,
                        //                 'tel_poweralert' => $telemetryData->tel_poweralert ?? null,
                        //                 'tel_speedalert' => $telemetryData->tel_speedalert ?? null,
                        //                 'tel_boxalert' => $telemetryData->tel_boxalert ?? null,
                        //                 'tel_fuel' => $telemetryData->tel_fuel ?? 0.0,
                        //                 'tel_rfid' => $telemetryData->tel_rfid ?? null,
                        //             ]);
                        //         }
                        //     }
                        // }
                    } else {
                        if (is_numeric($row['first_row_tel_temperature']) && $row['first_row_tel_temperature'] > 40) {
                            $row['first_row_tel_temperature'] = 'NW';
                        }
                    }
                }

                // Temperature correction for second row (afternoon)
                if (is_numeric($row['first_row_tel_temperature']) && ($row['second_row_tel_temperature'] == 'NW' || (is_numeric($row['second_row_tel_temperature']) && $row['second_row_tel_temperature'] > 40))) {
                    $maxTempEntry = TemperatureSummary::where('sys_service_id', $service_id)
                        ->where('temp_date', $currentDate->format('Y-m-d'))
                        ->first();

                    if ($maxTempEntry) {
                        $row['second_row_tel_temperature'] = $maxTempEntry->max_temp;

                        // if (!$isExport) {
                        //     $telemetryData = DB::table('latest_telemetry')
                        //         ->join('temperature_summary', 'temperature_summary.sys_service_id', '=', 'latest_telemetry.sys_service_id')
                        //         ->where('temperature_summary.sys_service_id', $service_id)
                        //         ->where('temperature_summary.temp_date', $currentDate->format('Y-m-d'))
                        //         ->select('latest_telemetry.*', 'temperature_summary.*')
                        //         ->first();

                        //     if ($telemetryData) {
                        //         $startTime = Carbon::createFromFormat('H:i:s', '12:00:00');
                        //         for ($i = 0; $i < 720; $i++) {
                        //             $currentGpsTime = $startTime->copy()->addMinute($i);
                        //             DB::table('telemetry_new_chamber')->insert(
                        //                 [
                        //                     'sys_service_id' => $telemetryData->sys_service_id,
                        //                     'sys_msg_type' => $telemetryData->sys_msg_type,
                        //                     'sys_proc_time' => $currentDate->format('Y-m-d') . ' ' . $currentGpsTime->format('H:i:s'),
                        //                     'sys_proc_host' => $telemetryData->sys_proc_host ?? null,
                        //                     'sys_asset_id' => $telemetryData->sys_asset_id ?? null,
                        //                     'sys_geofence_id' => $telemetryData->sys_geofence_id ?? null,
                        //                     'sys_device_id' => $telemetryData->sys_device_id ?? 0,
                        //                     'gps_date' => $currentDate->format('Y-m-d'),
                        //                     'gps_time' => $telemetryData->gps_time,
                        //                     'gps_latitude' => $telemetryData->gps_latitude ?? 0.0,
                        //                     'gps_longitude' => $telemetryData->gps_longitude ?? 0.0,
                        //                     'gps_orientation' => $telemetryData->gps_orientation ?? 0.0,
                        //                     'gps_speed' => $telemetryData->gps_speed ?? 0.0,
                        //                     'gps_fix' => $telemetryData->gps_fix ?? null,
                        //                     'geo_street' => $telemetryData->geo_street ?? null,
                        //                     'geo_town' => $telemetryData->geo_town ?? null,
                        //                     'geo_country' => $telemetryData->geo_country ?? null,
                        //                     'geo_postcode' => $telemetryData->geo_postcode ?? null,
                        //                     'jny_distance' => $telemetryData->jny_distance ?? null,
                        //                     'jny_duration' => $telemetryData->jny_duration ?? null,
                        //                     'jny_idle_time' => $telemetryData->jny_idle_time ?? null,
                        //                     'jny_status' => $telemetryData->jny_status ?? '0',
                        //                     'jny_leg_code' => $telemetryData->jny_leg_code ?? null,
                        //                     'jny_device_jny_id' => $telemetryData->jny_device_jny_id ?? null,
                        //                     'des_movement_id' => $telemetryData->des_movement_id ?? null,
                        //                     'des_vehicle_id' => $telemetryData->des_vehicle_id ?? null,
                        //                     'tel_state' => $telemetryData->tel_state ?? 0,
                        //                     'tel_ignition' => $telemetryData->tel_ignition ?? null,
                        //                     'tel_alarm' => $telemetryData->tel_alarm ?? null,
                        //                     'tel_panic' => $telemetryData->tel_panic ?? null,
                        //                     'tel_shield' => $telemetryData->tel_shield ?? null,
                        //                     'tel_theft_attempt' => $telemetryData->tel_theft_attempt ?? null,
                        //                     'tel_tamper' => $telemetryData->tel_tamper ?? null,
                        //                     'tel_ext_alarm' => $telemetryData->tel_ext_alarm ?? null,
                        //                     'tel_journey' => $telemetryData->tel_journey ?? null,
                        //                     'tel_journey_status' => $telemetryData->tel_journey_status ?? null,
                        //                     'tel_idle' => $telemetryData->tel_idle ?? null,
                        //                     'tel_ex_idle' => $telemetryData->tel_ex_idle ?? null,
                        //                     'tel_hours' => $telemetryData->tel_hours ?? null,
                        //                     'tel_input_0' => $telemetryData->tel_input_0 ?? null,
                        //                     'tel_input_1' => $telemetryData->tel_input_1 ?? null,
                        //                     'tel_input_2' => $telemetryData->tel_input_2 ?? null,
                        //                     'tel_input_3' => $telemetryData->tel_input_3 ?? null,
                        //                     'tel_temperature' => $maxTempEntry->max_temp,
                        //                     'tel_voltage' => $telemetryData->tel_voltage ?? 0.0,
                        //                     'main_powervoltage' => $telemetryData->main_powervoltage ?? 0.0,
                        //                     'tel_odometer' => $telemetryData->tel_odometer ?? null,
                        //                     'tel_poweralert' => $telemetryData->tel_poweralert ?? null,
                        //                     'tel_speedalert' => $telemetryData->tel_speedalert ?? null,
                        //                     'tel_boxalert' => $telemetryData->tel_boxalert ?? null,
                        //                     'tel_fuel' => $telemetryData->tel_fuel ?? 0.0,
                        //                     'tel_rfid' => $telemetryData->tel_rfid ?? null,
                        //                 ]
                        //             );
                        //         }
                        //     }
                        // }
                    }
                }

                $formattedChambers[] = $row;
                $currentDate->addDay();
            }
        }
        return $formattedChambers;
    }

    public function index()
    {
        set_time_limit(10000);

        $unique_id = [135892,63874,63325];
        
        // $unique_id = [63330,63904,63812,63216,63905,63813,12422047,63870,12422046,63872,63736,63031,63035,63837,63057,63713,63034,166020,63603,54448,63606,63326,63709,63367,63517,63342,63364,54850,63816,63879,63366,63815,141401,63869,63365,54851,141400,68182,12422048,141454,173031,68181,185994,188103,12435326,63903,64034,64033,63453,186935,63710,63452,63509,186916,12435327,63522,63871,63345,63420,141453,63344,12435325,12435329,64229,63343,55656,12435324,12435328,63120,63346,63237,54429,55215,128808,63843,141431,63240,63549,141432,141399,63427,64264,63348,63423,63323,129072,63839,56290,135910,63408,129360,63643,63491,62364,63873,63479,63503,63604,63499,63605,63165,63184,63428,63492,63206];
        // $unique_id = [63277,63167,63038,63779,63217,63490,63246,63780,63251,55310,63136,62392,63249,63238,63392,63797,63163,63419,135082,63135,63472,63642,63514,63321,63036,63487,55147,63521,63838,63276,63040,63393,64071,70695,63042,63972,58530,63406,63039,63194,129002,56312,63488,135909,63044,63119,63646,63193,63978,63524,63877,63667,63601,62393,63480,63818,63218,63421,63463,63400,63502,54861,63195,63328,62451,63878,63817,53996,63665,63154,63440,54268,63404,63429,63552,63430,63347,63405,63241,63045,64048,63355,63886,147335,63041,63157,63200,135302,63155,63391,63236,63516,63250,70752,63515,63875,54266,147162,70728,63394,63525,147163,63501,54267,63407,147164,63880,63164,54906,63741,147161];
        // $unique_id = [135892,147165,63245,62423,54430,63874,63325,147247,135514,63043,62476,63278,63737,63881,63243,62555,55636,63809,63259,55116,52227,135102,52228,54200,52226,187168,147160,128200,128353,52209,52345,52342,52344,52343,63273,63274,63262,175275,174736,175034,173366,52348,63166,129359,54454,127943,63201,166035,63422,63518,63033,63032,63056,63329,70802,63199,63322,128066,63842,63841,63239,141430,141433,63756,64027,64031,64029,63203,54374,54786,63489,63162,63937,63936,63935,63938,63324,52282,151346,51078,52280,63159,128827,12422045,12422043,63037,63666,64032,64030,64028,63242,68183,63836,53995,63814,136480];
        
        $startDate = Carbon::createFromFormat('Y-m-d', '2025-06-01');
        $endDate = Carbon::createFromFormat('Y-m-d', '2025-06-31');

        $formattedChambers = $this->processChambersData($unique_id, $startDate, $endDate);

        return view('chambers.index', compact('formattedChambers'));
    }

    public function export()
    {
        set_time_limit(10000);

        // $unique_id = [63343];

        // $unique_id = [63330,63904,63812,63216,63905,63813,12422047,63870,12422046,63872,63736,63031,63035,63837,63057,63713,63034,166020,63603,54448,63606,63326,63709,63367,63517,63342,63364,54850,63816,63879,63366,63815,141401,63869,63365,54851,141400,68182,12422048,141454,173031,68181,185994,188103,12435326,63903,64034,64033,63453,186935,63710,63452,63509,186916,12435327,63522,63871,63345,63420,141453,63344,12435325,12435329,64229,63343,55656,12435324,12435328,63120,63346,63237,54429,55215,128808,63843,141431,63240,63549,141432,141399,63427,64264,63348,63423,63323,129072,63839,56290,135910,63408,129360,63643,63491,62364,63873,63479,63503,63604,63499,63605,63165,63184,63428,63492,63206];
        // $unique_id = [63277,63167,63038,63779,63217,63490,63246,63780,63251,55310,63136,62392,63249,63238,63392,63797,63163,63419,135082,63135,63472,63642,63514,63321,63036,63487,55147,63521,63838,63276,63040,63393,64071,70695,63042,63972,58530,63406,63039,63194,129002,56312,63488,135909,63044,63119,63646,63193,63978,63524,63877,63667,63601,62393,63480,63818,63218,63421,63463,63400,63502,54861,63195,63328,62451,63878,63817,53996,63665,63154,63440,54268,63404,63429,63552,63430,63347,63405,63241,63045,64048,63355,63886,147335,63041,63157,63200,135302,63155,63391,63236,63516,63250,70752,63515,63875,54266,147162,70728,63394,63525,147163,63501,54267,63407,147164,63880,63164,54906,63741,147161];
        $unique_id = [135892,147165,63245,62423,54430,63874,63325,147247,135514,63043,62476,63278,63737,63881,63243,62555,55636,63809,63259,55116,52227,135102,52228,54200,52226,187168,147160,128200,128353,52209,52345,52342,52344,52343,63273,63274,63262,175275,174736,175034,173366,52348,63166,129359,54454,127943,63201,166035,63422,63518,63033,63032,63056,63329,70802,63199,63322,128066,63842,63841,63239,141430,141433,63756,64027,64031,64029,63203,54374,54786,63489,63162,63937,63936,63935,63938,63324,52282,151346,51078,52280,63159,128827,12422045,12422043,63037,63666,64032,64030,64028,63242,68183,63836,53995,63814,136480];

        $startDate = Carbon::createFromFormat('Y-m-d', '2025-6-01');
        $endDate = Carbon::createFromFormat('Y-m-d', '2025-6-31');

        $formattedChambers = $this->processChambersData($unique_id, $startDate, $endDate, true);

        return Excel::download(new ChambersExport($formattedChambers), 'chambers.xlsx');
    }

    public function exportMissingData()
    {
        set_time_limit(10000);

        // $unique_id = [63330,63904,63812,63216,63905,63813,12422047,63870,12422046,63872,63736,63031,63035,63837,63057,63713,63034,166020,63603,54448,63606,63326,63709,63367,63517,63342,63364,54850,63816,63879,63366,63815,141401,63869,63365,54851,141400,68182,12422048,141454,173031,68181,185994,188103,12435326,63903,64034,64033,63453,186935,63710,63452,63509,186916,12435327,63522,63871,63345,63420,141453,63344,12435325,12435329,64229,63343,55656,12435324,12435328,63120,63346,63237,54429,55215,128808,63843,141431,63240,63549,141432,141399,63427,64264,63348,63423,63323,129072,63839,56290,135910,63408,129360,63643,63491,62364,63873,63479,63503,63604,63499,63605,63165,63184,63428,63492,63206];
        // $unique_id = [63277,63167,63038,63779,63217,63490,63246,63780,63251,55310,63136,62392,63249,63238,63392,63797,63163,63419,135082,63135,63472,63642,63514,63321,63036,63487,55147,63521,63838,63276,63040,63393,64071,70695,63042,63972,58530,63406,63039,63194,129002,56312,63488,135909,63044,63119,63646,63193,63978,63524,63877,63667,63601,62393,63480,63818,63218,63421,63463,63400,63502,54861,63195,63328,62451,63878,63817,53996,63665,63154,63440,54268,63404,63429,63552,63430,63347,63405,63241,63045,64048,63355,63886,147335,63041,63157,63200,135302,63155,63391,63236,63516,63250,70752,63515,63875,54266,147162,70728,63394,63525,147163,63501,54267,63407,147164,63880,63164,54906,63741,147161];
        $unique_id = [135892,147165,63245,62423,54430,63874,63325,147247,135514,63043,62476,63278,63737,63881,63243,62555,55636,63809,63259,55116,52227,135102,52228,54200,52226,187168,147160,128200,128353,52209,52345,52342,52344,52343,63273,63274,63262,175275,174736,175034,173366,52348,63166,129359,54454,127943,63201,166035,63422,63518,63033,63032,63056,63329,70802,63199,63322,128066,63842,63841,63239,141430,141433,63756,64027,64031,64029,63203,54374,54786,63489,63162,63937,63936,63935,63938,63324,52282,151346,51078,52280,63159,128827,12422045,12422043,63037,63666,64032,64030,64028,63242,68183,63836,53995,63814,136480];
        
        $startDate = Carbon::createFromFormat('Y-m-d', '2025-04-01');
        $endDate = Carbon::createFromFormat('Y-m-d', '2025-04-30');

        $formattedChambers = $this->processChambersData($unique_id, $startDate, $endDate);

        $missingData = [];

        foreach ($formattedChambers as $chamber) {
            // Check for missing morning data
            if (strpos($chamber['message'], 'Morning data is missing') !== false) {
                $missingData[] = [
                    'date' => $chamber['first_row_date'],
                    'sys_service_id' => $chamber['sys_service_id'],
                    'time' => '10:00:00',
                    'temp' => $chamber['first_row_tel_temperature']
                ];
            }

            // Check for missing afternoon data
            if (strpos($chamber['message'], 'Afternoon data is missing') !== false) {
                $missingData[] = [
                    'date' => $chamber['second_row_date'],
                    'sys_service_id' => $chamber['sys_service_id'],
                    'time' => '17:00:00',
                    'temp' => $chamber['second_row_tel_temperature']
                ];
            }
        }

        // Generate the text file content
        $txtContent = '';
        foreach ($missingData as $data) {
            $txtContent .= $data['date'] . ',' . $data['sys_service_id'] . ',' . $data['time'] . ',' . $data['temp'] . "\n";
        }

        // Return the text file as a download
        return response($txtContent)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="missing_chambers_data.txt"');
    }
}