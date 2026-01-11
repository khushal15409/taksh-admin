<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeRepository
{
    public function __construct(protected User $employee)
    {
    }

    public function add(array $data): string|object
    {
        try {
            $employee = $this->employee->create($data);
            \Log::info('Employee created successfully', ['id' => $employee->id, 'email' => $employee->email]);
            return $employee;
        } catch (\Exception $e) {
            \Log::error('EmployeeRepository Add Error', [
                'data' => $data,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function getFirstWhere(array $params, array $relations = []): ?Model
    {
        return $this->employee->with($relations)->where($params)->first();
    }

    public function getList(array $orderBy = [], array $relations = [], int|string $dataLimit = 25, int $offset = null): Collection|LengthAwarePaginator
    {
        return $this->employee->with($relations)->latest()->paginate($dataLimit);
    }

    public function getListWhere(string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = 25, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);

        $query = $this->employee->with($relations)
            ->where('role_id', '!=', 1)
            ->where($filters);
            
        // Apply pincode scope if user has pincode_id
        if (auth()->check() && auth()->user()->pincode_id) {
            $query->where('pincode_id', auth()->user()->pincode_id);
        }

        return $query
            ->when(isset($key) && !empty($key[0]), function ($query) use ($key) {
                $query->where(function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->orWhere('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()
            ->paginate($dataLimit);
    }

    public function getPincodeWiseListWhere(string $pincodeId = 'all', string $searchValue = null, array $filters = [], array $relations = [], int|string $dataLimit = 25, int $offset = null): Collection|LengthAwarePaginator
    {
        $key = explode(' ', $searchValue);
        
        $query = $this->employee->where('role_id', '!=', 1)
            ->with($relations)
            ->where($filters);
            
        // Apply pincode scope if user has pincode_id
        if (auth()->check() && auth()->user()->pincode_id) {
            $query->where('pincode_id', auth()->user()->pincode_id);
        }
        
        return $query
            ->when(is_numeric($pincodeId), function($query) use($pincodeId) {
                return $query->where('pincode_id', $pincodeId);
            })
            ->when(isset($key) && !empty($key[0]), function ($query) use ($key) {
                $query->where(function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->orWhere('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()
            ->paginate($dataLimit);
    }

    public function update(string $id, array $data): bool|string|object
    {
        $employee = $this->employee->find($id);
        if (!$employee) {
            return false;
        }
        
        foreach ($data as $key => $column) {
            $employee[$key] = $column;
        }
        $employee->save();
        return $employee;
    }

    public function delete(string $id): bool
    {
        $query = $this->employee->where('role_id', '!=', 1);
        if (auth('admin')->check() && auth('admin')->user()->zone_id) {
            $query->where('zone_id', auth('admin')->user()->zone_id);
        }
        $employee = $query->find($id);
        if (!$employee) {
            return false;
        }
        
        $employee->delete();
        return true;
    }

    public function getSearchList(Request $request): Collection
    {
        $key = explode(' ', $request['search']);
        $query = $this->employee->where('role_id', '!=', 1);
        if (auth('admin')->check() && auth('admin')->user()->zone_id) {
            $query->where('zone_id', auth('admin')->user()->zone_id);
        }
        return $query
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%");
                }
            })
            ->limit(50)
            ->get();
    }

    public function getFirstWhereExceptAdmin(array $params, array $relations = []): ?Model
    {
        $query = $this->employee->where('role_id', '!=', 1)->with($relations)->where($params);
        if (auth('admin')->check() && auth('admin')->user()->zone_id) {
            $query->where('zone_id', auth('admin')->user()->zone_id);
        }
        return $query->first();
    }

    public function getExportList(Request $request): Collection
    {
        $key = explode(' ', $request['search']);
        $query = $this->employee->where('role_id', '!=', 1);
        if (auth('admin')->check() && auth('admin')->user()->zone_id) {
            $query->where('zone_id', auth('admin')->user()->zone_id);
        }
        return $query
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%");
                }
            })
            ->latest()
            ->get();
    }
}

