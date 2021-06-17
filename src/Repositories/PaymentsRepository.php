<?php

namespace EscolaLms\Payments\Repositories;

use EscolaLms\Core\Dtos\CriteriaDto;
use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Dtos\PaginationDto;
use EscolaLms\Core\Repositories\BaseRepository;
use EscolaLms\Payments\Facades\Payments;
use EscolaLms\Payments\Repositories\Contracts\PaymentsRepositoryContract;
use Illuminate\Database\Eloquent\Builder;

class PaymentsRepository extends BaseRepository implements PaymentsRepositoryContract
{
    public function getFieldsSearchable()
    {
        return [
            'billable_id',
            'payable_id',
            'status',
        ];
    }

    public function model()
    {
        return Payments::getPaymentsConfig()->getPaymentModel();
    }

    public function searchOrderAndPaginate(
        ?CriteriaDto $criteriaDto,
        ?OrderDto $orderDto,
        ?PaginationDto $paginationDto
    ): Builder {
        $query = $this->model->newQuery();
        if ($criteriaDto) {
            $query = $this->applyCriteriaDto($query, $criteriaDto);
        }
        if ($paginationDto) {
            $query = $this->applyPaginationDto($query, $paginationDto);
        }
        if ($orderDto) {
            $query = $this->applyOrderDto($query, $orderDto);
        }
        return $query;
    }

    public function applyOrderDto(Builder $query, OrderDto $dto): Builder
    {
        if ($dto->getOrder() && $dto->getOrderBy()) {
            return $query->orderBy($dto->getOrderBy(), $dto->getOrder());
        }
        return $query;
    }

    public function applyCriteriaDto(Builder $query, CriteriaDto $dto): Builder
    {
        return $this->applyCriteria($query, $dto->toArray());
    }
}
