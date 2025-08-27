<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVeterinaryVisitRequest;
use App\Http\Requests\UpdateVeterinaryVisitRequest;
use App\Http\Resources\VeterinaryVisitResource;
use App\Models\Cat;
use App\Models\VeterinaryVisit;
use App\Services\VeterinaryVisitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class VeterinaryVisitController extends Controller
{
    protected $veterinaryVisitService;

    public function __construct(VeterinaryVisitService $veterinaryVisitService)
    {
        $this->veterinaryVisitService = $veterinaryVisitService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('view', VeterinaryVisit::class);

        $visits = $this->veterinaryVisitService->getUserVisits(auth()->id());
        return VeterinaryVisitResource::collection($visits);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVeterinaryVisitRequest $request): JsonResponse
    {
        $this->authorize('create', VeterinaryVisit::class);

        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();

        $visits = $this->veterinaryVisitService->createVisit($validatedData);

        return response()->json([
            'data' => new VeterinaryVisitResource($visits),
            'message' => 'Запись о визите к ветеринару успешно создана'
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(VeterinaryVisit $veterinaryVisit): VeterinaryVisitResource
    {
        $this->authorize('view', $veterinaryVisit);

        return new VeterinaryVisitResource($veterinaryVisit->load('cat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVeterinaryVisitRequest $request, VeterinaryVisit $veterinaryVisit): VeterinaryVisitResource
    {
        $this->authorize('update', $veterinaryVisit);

        $validatedData = $request->validated();
        $this->veterinaryVisitService->updateVisit($veterinaryVisit, $validatedData);

        return new VeterinaryVisitResource($veterinaryVisit->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VeterinaryVisit $veterinaryVisit): JsonResponse
    {
        $this->authorize('delete', $veterinaryVisit);

        $this->veterinaryVisitService->deleteVisit($veterinaryVisit);

        return response()->json([
            'message' => 'Запись о визите к ветеринару успешно удалена'
        ], Response::HTTP_OK);
    }

    public function indexByCat(Cat $cat): AnonymousResourceCollection
    {
        $this->authorize('view', $cat);

        $visits = $this->veterinaryVisitService->getVisitsByCat($cat->id);

        return VeterinaryVisitResource::collection($visits);
    }

    public function storeForCat(StoreVeterinaryVisitRequest $request, Cat $cat): JsonResponse
    {
        $this->authorize('view', $cat);
        $this->authorize('create', VeterinaryVisit::class);

        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $validatedData['cat_id'] = $cat->id;

        $visit = $this->veterinaryVisitService->createVisit($validatedData);

        return response()->json([
            'data' => new VeterinaryVisitResource($visit),
            'message' => 'Запись о визите к ветеринару успешно добавлена'
        ], Response::HTTP_CREATED);
    }

    public function getUpcomingVisits(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', VeterinaryVisit::class);

        $upcomingVisits = $this->veterinaryVisitService->getUpcomingVisits(auth()->id(), 7);
        return VeterinaryVisitResource::collection($upcomingVisits);
    }

    public function getStats(): JsonResponse
    {
        $this->authorize('viewAny', VeterinaryVisit::class);

        $stats = $this->veterinaryVisitService->getVisitStats(auth()->id());
        return response()->json(['stats' => $stats]);
    }

    public function createVisit(StoreVeterinaryVisitRequest $request)
    {
        $this->authorize('create', VeterinaryVisit::class);

        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();

            $visit = $this->veterinaryVisitService->createVisit($data);
            return new VeterinaryVisitResource($visit);
        } catch (InvalidArgumentException $exception) {
            return response()->json(['error' => $exception->getMessage()], 422);
        }
    }
}
