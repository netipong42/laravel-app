<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerController extends Controller
{

    public function findAll(Request $request)
    {
        $data = $request->validate([
            'page' => 'nullable|numeric',
            'per_page' => 'nullable|numeric|min:1',
            'name' => 'nullable|string|max:200',
            'email' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:20',
        ]);

        $query = Customer::query()
            ->when($data['name'] ?? null, function ($q, $name) {
                $q->where('name', 'like', "%{$name}%");
            })
            ->when($data['email'] ?? null, function ($q, $email) {
                $q->where('email', 'like', "%{$email}%");
            })
            ->when($data['phone'] ?? null, function ($q, $phone) {
                $q->where('phone', 'like', "%{$phone}%");
            });

        $dataCustomer = $query->paginate($data['per_page'] ?? 20);

        return response()->json([
            'status' => 'success',
            'message' => 'Get Data customers successfully',
            'data' => CustomerResource::collection($dataCustomer),
            'meta' => [
                'current_page' => $dataCustomer->currentPage(),
                'total_page' => $dataCustomer->lastPage(),
                'per_page' => $dataCustomer->perPage(),
                'total' => $dataCustomer->total(),
            ]
        ]);
    }

    public function findById(Customer $customer)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Get Data customer successfully',
            'data' => new CustomerResource($customer),
            'meta' => []
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|unique:customers,email|email',
            'phone' => 'required|string|max:20',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'email' => Str::lower($request->email),
            'phone' => $request->phone,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Customer created successfully',
            'data' => new customerResource($customer),
            'meta' => []
        ], 201);
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:20',
        ]);

        $customer->update([
            'name' => $request->name,
            'email' => Str::lower($request->email),
            'phone' => $request->phone,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Customer updated successfully',
            'data' => new customerResource($customer),
            'meta' => []
        ]);
    }

    public function delete(Customer $customer)
    {
        $customer->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Customer deleted successfully',
            'data' => null,
            'meta' => []
        ]);
    }
}
