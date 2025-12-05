<?php

namespace App\Http\Controllers;

use App\Models\Service;
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

    protected function resource_get_list($data)
    {
        return $data;
    }
    public function index(Request $request)
    {

        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);



        $paginated_result = $this->service->getPaginated($page, $perPage);
        $result = [
            'data' => $this->resource_get_list($paginated_result->items()),

            'pagination' => [
                'current_page' => $paginated_result->currentPage(),
                'per_page' => $paginated_result->perPage(),
                'total' => $paginated_result->total(),
                'last_page' => $paginated_result->lastPage(),
            ]
        ];
        if (request()->wantsJson()) return $result;
        // return Inertia::render('RoomTemplates/Index', $result);
        return $this->index_view();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (request('id')) {

            $data = $request->validate($this->updateRequestRules());
        } else {
            $data = $request->validate($this->storeRequestRules());
        }
        try {
            $data = $this->setImages($data);
        } catch (\Throwable $th) {
            return $this->responseError(['message' => $th->getMessage()]);
        }

        if (request('id')) {
            $record = $this->service->update(request('id'), $data);
        } else {
            $record = $this->service->create($data);
        }
        if (request()->wantsJson()) return response()->json([
            'message' => 'record created successfully',
            'record' => $record
        ]);

        $this->responseSuccess($record, 'record created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $record = $this->service->getById($id);

        if (!$record) {
            $this->responseError(['message' => 'record not found']);
        }

        $this->responseSuccess($record, 'record created successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate($this->updateRequestRules());
        try {
            $data = $this->setImages($data);
        } catch (\Throwable $th) {
            return $this->responseError(['message' => $th->getMessage()]);
        }

        $record = $this->service->update($id, $data);

        if (!$record) {
            return back()->with(['message' => 'record not found']);
        }
        $this->responseSuccess($record, 'record updated successfully');
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
        $this->responseSuccess([], 'record deleted successfully');
    }

    abstract protected function storeRequestRules(): array;
    abstract protected function updateRequestRules(): array;
    abstract protected function index_view();

    protected function setImages($data)
    {
        return $data;
    }
}
