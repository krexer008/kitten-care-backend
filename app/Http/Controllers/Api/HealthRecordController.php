<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHealthRecordRequest;
use App\Http\Requests\UpdateHealthRecordRequest;
use App\Http\Resources\HealthRecordResource;
use App\Models\Cat;
use App\Models\HealthRecord;
use App\Services\HealthRecordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class HealthRecordController extends Controller
{

    protected HealthRecordService $healthRecordService;

    public function __construct(HealthRecordService $healthRecordService)
    {
        $this->healthRecordService = $healthRecordService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', HealthRecord::class);
        $records = $this->healthRecordService->getUserRecords(auth()->id());
        return HealthRecordResource::collection($records);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHealthRecordRequest $request): JsonResponse
    {
        $this->authorize('create', HealthRecord::class);

        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();

        $record = $this->healthRecordService->createRecord($validatedData);

        return response()->json([
            'data' => new HealthRecordResource($record),
            'message' => 'Запись о здоровье успешно создана'
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(HealthRecord $healthRecord): HealthRecordResource
    {
        $this->authorize('view', $healthRecord);
        return new HealthRecordResource($healthRecord->load('cat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHealthRecordRequest $request, HealthRecord $healthRecord): HealthRecordResource
    {
        $this->authorize('update', $healthRecord);

        $validatedData = $request->validated();
        $this->healthRecordService->updateRecord($healthRecord, $validatedData);

        return new HealthRecordResource($healthRecord->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HealthRecord $healthRecord): JsonResponse
    {
        $this->authorize('delete', $healthRecord);
        $this->healthRecordService->deleteRecord($healthRecord);

        return response()->json([
            'message' => 'Запись о здоровье успешно удалена'
        ], Response::HTTP_OK);
    }

    public function indexByCat(Cat $cat): AnonymousResourceCollection
    {
        $this->authorize('view', $cat);

        $records = $this->healthRecordService->getRecordsByCat($cat->id);
        return HealthRecordResource::collection($records);
    }

    public function storeForCat(StoreHealthRecordRequest $request, Cat $cat): JsonResponse
    {
        $this->authorize('view', $cat);

        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $validatedData['cat_id'] = $cat->id();

        $records = $this->healthRecordService->createRecord($validatedData);

        return response()->json([
            'data' => new HealthRecordResource($records),
            'message' => 'Запись о здоровье успешно добавлена'
        ], Response::HTTP_CREATED);
    }
}
