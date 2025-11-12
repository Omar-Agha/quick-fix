<?php

namespace App\Http\Controllers;

use App\Services\BaseCrudService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Inertia\Response;

abstract class BaseCrudController extends Controller
{
    public function __construct(
        private BaseCrudService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {

        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);



        $paginated_result = $this->service->getPaginated($page, $perPage);
        $result = [
            'data' => $paginated_result->items(),

            'pagination' => [
                'current_page' => $paginated_result->currentPage(),
                'per_page' => $paginated_result->perPage(),
                'total' => $paginated_result->total(),
                'last_page' => $paginated_result->lastPage(),
            ]
        ];
        // if (request()->wantsJson()) return $result;
        // return Inertia::render('RoomTemplates/Index', $result);
        return $this->index_view();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->storeRequestRules());

        if (request('id')) {
            $record = $this->service->update($data['id'], $data);
        } else {
            $record = $this->service->create($data);
        }


        return back()->with('success', 'record created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $record = $this->service->getById($id);

        if (!$record) {
            return response()->json(['message' => 'record not found'], 404);
        }

        return response()->json(['record' => $record]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate($this->updateRequestRules());

        $workout = $this->service->update($id, $data);

        if (!$workout) {
            return back()->with(['message' => 'record not found']);
        }
        return back()->with(['message' => 'record updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return back()->with(['message' => 'record not found']);
        }
        return back()->with('success', ['message' => 'record deleted successfully']);
    }

    abstract protected function storeRequestRules(): array;
    abstract protected function updateRequestRules(): array;
    abstract protected function index_view(): Response;
}
