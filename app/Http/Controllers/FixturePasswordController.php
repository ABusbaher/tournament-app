<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckFixturePasswordRequest;
use App\Http\Requests\UpdateFixturePasswordRequest;
use App\Models\Tournament;
use App\Services\FixturePasswordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class FixturePasswordController extends Controller
{
    protected FixturePasswordService $fixturePasswordService;
    public function __construct(FixturePasswordService $fixturePasswordService)
    {
        $this->fixturePasswordService = $fixturePasswordService;
    }

    public function checkPassword(CheckFixturePasswordRequest $request, Tournament $tournament, $fixture): JsonResponse
    {
        $validatedData = $request->validated();
        try {
            $this->fixturePasswordService->checkFixturePassword($tournament, $fixture, $validatedData['password']);
            return response()->json(['password' => 'valid']);
        } catch (InvalidArgumentException $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (Throwable $t) {
            Log::error($t->getMessage());
            return response()->json(['message' => 'An error occurred while checking password.'], 500);
        }
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateFixturePasswordRequest $request, Tournament $tournament, $fixture): JsonResponse
    {
        $this->authorize('adminAccess', $request->user());
        $validatedData = $request->validated();
        try {
            $password = $this->fixturePasswordService->updateFixturePassword($validatedData, $tournament, $fixture);
            return response()->json(['password' => $password]);
         } catch (InvalidArgumentException $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (Throwable $t) {
            Log::error($t->getMessage());
            return response()->json(['message' => 'An error occurred while updating fixture password.'], 500);
        }
    }
}
