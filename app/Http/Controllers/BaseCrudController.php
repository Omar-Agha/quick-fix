<?php

namespace App\Http\Controllers;



use App\Services\RoomTemplateService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class RoomTemplateController extends Controller
{
    public function __construct(
        private RoomTemplateService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);



        $paginated_result = $this->service->getPaginated($page, $perPage);
        $result = [
            'data' =>$paginated_result->items(),

            'pagination' => [
                'current_page' => $paginated_result->currentPage(),
                'per_page' => $paginated_result->perPage(),
                'total' => $paginated_result->total(),
                'last_page' => $paginated_result->lastPage(),
            ]
        ];
        if (request()->wantsJson()) return $result;
        return Inertia::render('RoomTemplates/Index', $result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'room_type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'currency' => 'required|string',
            'amenities' => 'required|array',
        ]);

    

        $new_record = $this->service->create($data);
        return back()->with('success', 'record created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $workout = $this->service->getById($id);

        if (!$workout) {
            return response()->json(['message' => 'record not found'], 404);
        }

        return response()->json(['workout' => $workout]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string',
            'numOfRooms' => 'required|integer',
            'numOfFloors' => 'required|integer',
        ]);

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
}
