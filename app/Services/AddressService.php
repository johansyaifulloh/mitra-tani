<?php

namespace App\Services;

use App\Repositories\AddressRepository;
use App\Support\FormatHelper;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddressService
{
    public function __construct(
        private AddressRepository $addressRepository,
    ) {}

    public function listForUser(int $userId): array
    {
        $rows = $this->addressRepository->listForUser($userId);

        return $rows->map(fn ($row) => FormatHelper::addressForView($row))->all();
    }

    public function create(int $userId, array $data): int
    {
        DB::beginTransaction();

        try {
            if (! empty($data['is_default'])) {
                $this->addressRepository->clearDefaultForUser($userId);
            }

            $id = $this->addressRepository->insert([
                'user_id' => $userId,
                'label' => $data['label'],
                'recipient_name' => $data['recipient_name'],
                'phone' => $data['phone'],
                'street' => $data['street'],
                'district' => $data['district'],
                'is_default' => ! empty($data['is_default']),
            ]);

            DB::commit();

            return $id;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('AddressService::create failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function update(int $userId, int $id, array $data): void
    {
        $address = $this->addressRepository->findById($id, $userId);

        if (! $address) {
            throw new Exception('Alamat tidak ditemukan.');
        }

        DB::beginTransaction();

        try {
            if (! empty($data['is_default'])) {
                $this->addressRepository->clearDefaultForUser($userId);
            }

            $this->addressRepository->update($id, array_filter([
                'label' => $data['label'] ?? null,
                'recipient_name' => $data['recipient_name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'street' => $data['street'] ?? null,
                'district' => $data['district'] ?? null,
                'is_default' => array_key_exists('is_default', $data) ? (bool) $data['is_default'] : null,
            ], fn ($v) => $v !== null));

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('AddressService::update failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function delete(int $userId, int $id): void
    {
        $address = $this->addressRepository->findById($id, $userId);

        if (! $address) {
            throw new Exception('Alamat tidak ditemukan.');
        }

        $this->addressRepository->delete($id);
    }

    public function hasAny(int $userId): bool
    {
        return $this->addressRepository->countForUser($userId) > 0;
    }
}
