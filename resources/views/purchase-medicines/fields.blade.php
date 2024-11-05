<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive-sm medicinePurchaseCreateTable">
            <div class="overflow-auto">
            <table class="table table-striped" id="prescriptionMedicalTbl">
                <thead class="thead-dark">
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="">{{ __('messages.medicines') }}<span class="required"></span></th>
                    <th class="">{{ __('lot no.') }}<span class="required"></span></th>
                    <th class="">{{ __('Expiry Date') }}<span class="required"></span></th>
                    <th class="">{{ __('Sale Price') }}<span class="required"></span></th>
                    <th class="">{{ __('Purchase Price') }}<span class="required"></span></th>
                    <th class="">{{ __('Quantity') }}<span class="required"></span></th>
                    <th class="">{{ __('Tax') }}</th>
                    <th class="">{{ __('Amount') }}<span class="required"></span></th>
                    <th class="table__add-btn-heading text-center form-label fw-bolder text-gray-700 mb-3">
                        <a href="javascript:void(0)" type="button"
                           class="btn btn-primary text-star add-medicine-btn-purchase">
                            {{ __('messages.common.add') }}
                        </a>
                    </th>
                </tr>
                </thead>
                <tbody class="prescription-medicine-container">
                <tr>
                    <td>
                        {{ Form::select('medicine[]', $medicines['medicines'], null,['required','class' => 'form-select purchaseMedicineId','placeholder'=>'Select Medicine','id'=>'medicineChooseId1','data-control'=>'select2','data-id'=>1]) }}
                    </td>
                    <td>
                        {{ Form::number('lot_no[]', null, ['required','class' => 'form-control', 'id' => 'lot_no1','placeholder'=>'Lot no.']) }}
                    </td>
                    <td>
                        {{ Form::text('expiry_date[]', null, ['required','class' => 'form-control bg-white purchaseMedicineExpiryDate', 'id' => 'expiry_date1','placeholder'=>__('messages.purchase_medicine.expiry_date')]) }}
                    </td>
                    <td>
                        {{ Form::number('sale_price[]', '0.00',['required','class' => 'form-control prescriptionMedicineMealId', 'readonly','id' => 'sale_price1']) }}
                    </td>
                    <td>
                        {{ Form::number('purchase_price[]', '0.00', ['required','class' => 'form-control purchase-price', 'readonly', 'rows'=>1, 'id' => 'purchase_price1']) }}
                    </td>
                    <td>
                        {{ Form::number('quantity[]', 0, ['required','class' => 'form-control purchase-quantity' ,'id'=>'quantity1']) }}
                    </td>
                    <td style="white-space: pre">
                        <div class="input-group">
                        {{ Form::number('tax_medicine[]', 0, ['class' => 'form-control purchase-tax', 'rows'=>1, 'id'=>'tax1']) }}
                        <span class="input-group-text ms-0" id="amountTypeSymbol"> {{ __('%') }}</span>
                        </div>
                    </td>
                    <td>
                        {{ Form::number('amount[]','0.00', ['required','class' => 'form-control purchase-amount','readonly', 'rows'=>1,'id'=>'amount1',]) }}
                    </td>
                    <td class="text-center">
                        <a href="javascript:void(0)" title="{{__('messages.common.delete')}}"
                           class="delete-purchase-medicine-item btn px-1 text-danger fs-3 pe-0">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
            </div>
            <div class="row mt-5 justify-content-between">
                <div class="col-md-6 mb-md-0 mb-5">
                    <label class="fw-bold text-muted py-3">{{ __('messages.ipd_patient.notes')  }}</label>
                    {{ Form::textarea('note', null, ['class' => 'form-control', 'rows'=>2,'Note','placeholder' => __('messages.ipd_patient.notes')]) }}
                </div>
                <div class="col-xl-4 col-md-5">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <label class="fw-bold text-muted py-3 required">{{ __('Total')  }}</label>
                        </div>
                        <div>
                            {{ Form::hidden('purchase_no',generateUniquePurchaseNumber(), ['class' => 'form-control']) }}
                            {{ Form::number('total', '0.00', ['required','class' => 'form-control','readonly', 'rows'=>1,'id'=>'total',]) }}
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <label class="fw-bold text-muted py-3">{{ __('Discount')  }}</label>
                        </div>
                        <div>
                            {{ Form::number('discount','0.00', ['class' => 'form-control purchase-discount','id'=>'discountAmount']) }}
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <label class="fw-bold text-muted py-3">{{ __('Tax Amount')  }}</label>
                        </div>
                        <div>
                            <div class="input-group">
                            {{ Form::number('tax', '0.00', ['class' => 'form-control','id'=>'purchaseTaxId' ,'readonly','value'=>'0.00']) }}
                            {{--  <span class="input-group-text ms-0" id="amountTypeSymbol"> {{ __('$') }}</span>  --}}
                        </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <label class="fw-bold text-muted required py-3">{{ __('Net Amount')  }}</label>
                        </div>
                        <div>
                            {{ Form::number('net_amount', '0.00', ['required','class' => 'form-control','id'=>'netAmount','readonly']) }}
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <label class="fw-bold text-muted required py-3">{{ __('Payment Type')  }}</label>
                        </div>
                        <div>
                            {{ Form::select('payment_type', getPurchaseMedicinePaymentTypes(),null, ['required','class' => 'form-select', 'placeholder'=>'Payment Mode','id'=>'paymentMode']) }}
                        </div>
                    </div>
                    <div>
                        <label class="fw-bold text-muted py-3">{{ __('Payment Note')  }}</label>
                        {{ Form::textarea('payment_note',null, ['class' => 'form-control', 'placeholder'=>'Payment Note','rows'=>3]) }}
                    </div>
                    <div class="float-end mt-5">
                        {!! Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-2 saveBtnPurchaseMedicne' ]) !!}
                        <a href="{!! route('medicine-purchase.index') !!}"
                           class="btn btn-secondary">{!! __('messages.common.cancel') !!}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
