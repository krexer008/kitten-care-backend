<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCatRequest;
use App\Http\Requests\UpdateCatRequest;
use App\Http\Resources\CatResource;
use App\Models\Cat;
use App\Services\CatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class CatController extends Controller
{
    protected $catService;
    public function __construct(CatService $catService)
    {
        // Внедряем CatService через конструктор
        $this->catService = $catService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Cat::class);
        // Получаем всех котов текущего пользователя
        $cats = $this->catService->getUsersCats(auth()->id());
        // Возвращаем коллекцию котов через ресурс
        return CatResource::collection($cats);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCatRequest $request): JsonResponse
    {
        $this->authorize('create', Cat::class);
        // Данные уже валидированы в StoreCatRequest
        $validatedDate = $request->validated();
        // Создаем котика через сервис
        $cat = $this->catService->createCat($validatedDate, auth()->id());

        // Возвращаем созданного кота c HTTP-статусом 201 (Created)
        return response()->json([
            'data' => new CatResource($cat),
            'message' => 'Кот успешно добавлен'
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cat $cat): CatResource
    {
        // Автоматическое разрешение модели через Route Model Binding
        // Проверяем, что кот принадлежит текущему пользователю
        $this->authorize('view', $cat);

        // Загружаем связанные данные для подробного просмотра
        $cat->load('feedings', 'healthRecords', 'veterinaryVisits');

        return new CatResource($cat);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCatRequest $request, Cat $cat): CatResource
    {
        $this->authorize('update', $cat);

        $validatedData= $request->validated();

        // Обновляем кота через сервис
        $this->catService->updateCat($cat, $validatedData);

        // Возвращаем обновленного кота
        return new CatResource($cat->fresh()); // fresh() для получения актуальных данных из БД
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cat $cat): JsonResponse
    {
        $this->authorize('delete', $cat);

        // Удаляем кота через сервис
        $this->catService->deleteCat($cat);

        return response()->json([
            'message' => 'Кот удален'
        ], Response::HTTP_OK);
    }
}
