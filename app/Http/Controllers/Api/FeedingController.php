<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedingRequest;
use App\Http\Requests\UpdateFeedingRequest;
use App\Http\Resources\FeedingResource;
use App\Models\Cat;
use App\Models\Feeding;
use App\Services\FeedingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class FeedingController extends Controller
{
    protected $feedingService;

    public function __construct(FeedingService $feedingService)
    {
        $this->feedingService = $feedingService;
    }

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Feeding::class);
        $feedings = $this->feedingService->getUserFeedings(auth()->id());
        return FeedingResource::collection($feedings);
    }

    public function store(StoreFeedingRequest $request): JsonResponse
    {
        $this->authorize('create', Feeding::class);
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();

        $feeding = $this->feedingService->createFeeding($validatedData);

        return response()->json([
            'data' => new FeedingResource($feeding),
            'message' => 'Кормление успешно добавлено'
        ], Response::HTTP_CREATED);
    }

    public function show(Feeding $feeding): FeedingResource
    {
        $this->authorize('view', $feeding);
        return new FeedingResource($feeding->load('cat'));
    }

    public function update(UpdateFeedingRequest $request, Feeding $feeding): FeedingResource
    {
        $this->authorize('update', $feeding);

        $validatedData = $request->validated();
        $this->feedingService->updateFeeding($feeding, $validatedData);

        return new FeedingResource($feeding->fresh());
    }

    public function destroy(Feeding $feeding): JsonResponse
    {
        $this->authorize('delete', $feeding);
        $this->feedingService->deleteFeeding($feeding);

        return response()->json([
            'message' => 'Запись о кормлении успешно удалена'
        ], Response::HTTP_OK);
    }

    public function indexByCat(Cat $cat): AnonymousResourceCollection
    {
        $this->authorize('view', $cat);

        $feedings = $this->feedingService->getFeedingsByCat($cat->id);
        return FeedingResource::collection($feedings);
    }

    public function storeForCat(StoreFeedingRequest $request, Cat $cat): JsonResponse
    {
        $this->authorize('view', $cat);
        $this->authorize('create', Feeding::class);

        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $validatedData['cat_id'] = $cat->id;

        $feeding = $this->feedingService->createFeeding($validatedData);

        return response()->json([
            'data' => new FeedingResource($feeding),
            'message' => 'Кормление успешно добавлено'
        ], Response::HTTP_CREATED);
    }

    public function getTodayFeedings(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Feeding::class);
        $feedings = $this->feedingService->getTodayFeedings(auth()->id());
        return FeedingResource::collection($feedings);
    }

    public function getDailyStats(Cat $cat, string $date): JsonResponse
    {
        $this->authorize('view', $cat);

        try {
            $stats = $this->feedingService->getDailyStats($cat->id, $date);
            return response()->json(['data' => $stats]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function getWeeklyStats(Cat $cat, string $startDate): JsonResponse
    {
        $this->authorize('view', $cat);

        try {
            $stats = $this->feedingService->getWeeklyStats($cat->id, $startDate);
            return response()->json(['data' => $stats]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
