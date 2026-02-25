<?php

namespace Appttpontrollers;

use Illuminatettpequest;
use Illuminateupportacadesroadcast;
use ApproadcastingSP32DataChannel;

class BroadcastController extends Controller
{
    /**
     * Handle ESP32 data broadcast request
     */
    public function broadcastESP32Data(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string|max:255',
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric',
            'pressure' => 'required|numeric',
            'valve_status' => 'required|boolean',
            'timestamp' => 'required|date',
        ]);

        // Broadcast data to ESP32DataChannel
        Broadcast::channel('esp32-data', ESP32DataChannel::class)->broadcast($validated);

        return response()->json([
            'message' => 'Data broadcasted successfully',
            'data' => $validated
        ]);
    }

    /**
     * Handle user presence on ESP32 channel
     */
    public function joinChannel(Request $request)
    {
        $validated = $request->validate([
            'channel_name' => 'required|string|max:255',
        ]);

        return response()->json([
            'message' => 'Joined channel successfully',
            'channel' => $validated['channel_name']
        ]);
    }
}
