<?php

namespace EscolaLms\Payments\Dtos;

use EscolaLms\Core\Dtos\Contracts\InstantiateFromRequest;
use EscolaLms\Core\Dtos\CriteriaDto;
use EscolaLms\Core\Repositories\Criteria\Primitives\EqualCriterion;
use EscolaLms\Payments\Contracts\Billable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PaymentFilterCriteriaDto extends CriteriaDto implements InstantiateFromRequest
{
    public static function instantiateFromRequest(Request $request): self
    {
        $criteria = new Collection();

        if (!is_null($request->input('status'))) {
            $criteria->push(new EqualCriterion('status', $request->input('status')));
        }
        if (!is_null($request->input('billable_id'))) {
            $criteria->push(new EqualCriterion('billable_id', $request->input('billable_id')));
        }
        if (!is_null($request->input('billable_type'))) {
            $criteria->push(new EqualCriterion('billable_type', $request->input('billable_type')));
        }
        if (!is_null($request->input('payable_id'))) {
            $criteria->push(new EqualCriterion('payable_id', $request->input('payable_id')));
        }
        if (!is_null($request->input('payable_type'))) {
            $criteria->push(new EqualCriterion('payable_type', $request->input('payable_type')));
        }

        return new self($criteria);
    }
}
