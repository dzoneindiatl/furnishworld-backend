@foreach ($groupedCombinations as $primaryId => $combos)
    @php
        $primaryValue = \App\Models\VariantValue::find($primaryId);
         $mainVariantValueId = \App\Models\ProductVariantValue::where('product_id', $product_id)
        ->where('is_main', '1')
        ->value('variant_value_id');

        $images = \App\Models\ProductGraphics::where('product_id', $product_id)->where('variant_id',$primaryId)->where('graphic_type','image')->get();
        $videos = \App\Models\ProductGraphics::where('product_id', $product_id)->where('variant_id',$primaryId)->where('graphic_type','video')->get();
        $imagePath = config('constant.PRODUCT_IMAGE_PATH');

        $variantCombinationOutOfStock = \App\Models\ProductVariantCombination::where('product_id', $product_id)
            ->whereJsonContains('combination_id',  (int) $primaryId)->where('is_out_of_stock', 1)
            ->count();
        
    @endphp
    <div class="card mb-6 shadow-sm variant_group_row" id="variant_{{ $primaryId }}">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <label for="variant_radio_{{ $primaryId }}" class="fw-bold mb-0 me-2">
                    Primary Variant: {{ $primaryValue->name }} — {{ count($combos) }} combinations
                </label>
            </div>
             <div class="" data-bs-toggle="collapse" href="#collapseExample_{{ $primaryId }}" role="button" aria-expanded="false" aria-controls="collapseExample_{{ $primaryId }}">
                <div class="form-check form-switch specialization-toggle">
                    <input class="form-check-input specialization-toggle"
                        type="checkbox"
                        data-variant-id="{{ $primaryId }}"
                        data-product-id="{{ $product_id }}"
                        id="specialization_toggle_{{ $primaryId }}" checked>     
                    <label class="form-check-label" for="specialization_toggle_{{ $primaryId }}">
                        Specification
                    </label>
                </div>
            </div>

            <!-- Out of Stock Toggle -->
            <div class="form-check form-switch">
                <input class="form-check-input out-of-stock-toggle"
                    type="checkbox"
                    data-variant-id="{{ $primaryId }}"
                    data-product-id="{{ $product_id }}"
                    id="out_of_stock_{{ $primaryId }}"
                    {{ $variantCombinationOutOfStock ? 'checked' : '' }}>
    
                <label class="form-check-label" for="out_of_stock_{{ $primaryId }}">
                    Out of Stock
                </label>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped variant-combo-table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Variant Name</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Discount Type</th>
                            <th>Discount</th>
                             <th>Sale Price</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody id="variant_body_{{ $primaryId }}">
                        @foreach ($combos as $combo)
                            @php
                                $ids = explode('_', $combo);
                                $variantValues = \App\Models\VariantValue::whereIn('id', $ids)->get()->keyBy('id');
                                $names = collect($ids)->map(fn($id) => $variantValues[$id]->name ?? '')->toArray();
                                $variantName = implode(' ', $names);
                                $variantSKU = strtolower(implode('_', $names));
                                
                                
                                $valueIds = array_map('intval', $ids);
                                $savedCombo = \App\Models\ProductVariantCombination::where('product_id', $product_id)
                                    ->where('combination_id',json_encode($valueIds))
                                    ->first();

                                $product = \App\Models\Product::where('id',$product_id)->first();

                            @endphp

                            <?php 
                            $combo_arr = explode('_',$combo);
                            $variant_id= '';
                            if(isset($combo_arr[0])){
                               $variant_id = $combo_arr[0]; 
                            }
                            ?>
                            <tr id="variant_combo_{{ $combo }}" 
                                data-combo-id="{{ $combo }}" 
                                data-variant-name="{{ $variantName }}" 
                                data-variant-sku="SKU_{{ $variantSKU }}">

                                <td>
                                    <span class="v_name">{{ $variantName }}</span>
                                    <input type="hidden" name="variant_name[]" value="{{ $variantName }}">
                                    <input type="hidden" name="combo[]" value="{{ $combo }}">
                                    <input type="hidden" name="variant_id[]" value="{{ $primaryId }}">
                                </td>

                                <td>
                                    <div class="input-group">
                                        <!-- <span class="input-group-text dynamic-sku-prefix">{{$product->sku ?? 'SKU_'}}</span> -->
                                        <input 
                                            type="text" 
                                            name="variant_sku[]" 
                                            class="v_input_sku form-control" 
                                            value="{{ $savedCombo->sku ?? strtolower($variantSKU) }}">
                                    </div>
                                </td>

                                <td>
                                    <input type="number" min="0" name="variant_price[]" 
                                        value="{{ $savedCombo->price ?? '' }}" 
                                        class="v_input_price form-control" placeholder="Price">
                                </td>

                                <td>
                                    <select name="variant_discount_type[]" class="discount_type_popup form-control">
                                        <option value="" {{ ($savedCombo->discount_type ?? '') === '' ? 'selected' : '' }}>None</option>
                                        <option value="flat" {{ ($savedCombo->discount_type ?? '') === 'flat' ? 'selected' : '' }}>Flat</option>
                                        <option value="percentage" {{ ($savedCombo->discount_type ?? '') === 'percentage' ? 'selected' : '' }}>%</option>
                                    </select>
                                </td>

                                <td>
                                    <input type="number" min="0" name="variant_discount[]" 
                                        value="{{ $savedCombo->discount ?? '' }}" 
                                        class="discount_popup form-control" placeholder="Discount">
                                </td>

                                <td>
                                    <input type="number" min="0" name="variant_sale_price[]" 
                                        value="{{ $savedCombo->selling_price ?? '' }}" 
                                        class="v_input_sale_price form-control" placeholder="Sale Price" readonly>
                                </td>

                                <td>
                                    <input type="number" min="0" name="variant_qty[]" 
                                        value="{{ $savedCombo->qty ?? '' }}" 
                                        class="v_input_quantity  input_quantity_{{ $variant_id }} form-control" placeholder="Qty">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div  id="collapseExample_{{ $primaryId }}">
                <div class="card card-body">
                    <label for="specialization_{{ $primaryId }}" class="form-label fw-semibold">Specification</label>
                    @if(!empty($productVariantSpecification) && count($productVariantSpecification) > 0)
                        @foreach($productVariantSpecification as $vl)
                            @if($vl->variant_value_id == $primaryId)
                                <textarea class="form-control ck_content" name="content_specification_{{ $primaryId }}" id="specialization_{{ $primaryId }}" rows="4">{!! $vl->{'content_' . $primaryId} ?? '' !!}</textarea>
                            @endif
                        @endforeach
                    @else 
                        <textarea class="form-control ck_content" name="content_specification_{{ $primaryId }}" id="specialization_{{ $primaryId }}" rows="4"></textarea>   
                    @endif          
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="button"
                    class="btn btn-primary update-variant-btn"
                    data-primary-id="{{ $primaryId }}"
                    data-product-id="{{ $product_id }}">
                    Update Variants
                </button>
            </div>
             
        </div>
    </div>
@endforeach


<script>
window.currentPrimaryId = '';

   onDocumentReady(function () {
    loadCkeditorScript(function () {
        initVariantSpecializationEditors(document);
    });

});

    function onDocumentReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
        } else {
            callback();
        }
    }

    function initVariantSpecializationEditors(container) {

    if (typeof CKEDITOR === 'undefined') {

        console.error('CKEDITOR not loaded');

        return;
    }

    if (!container) {

        console.error('CKEditor container not found');

        return;
    }

    CKEDITOR.config.allowedContent = true;

    $(container).find('.ck_content').each(function () {

        const textarea = this;

        // ID nahi hai to unique ID generate karo
        if (!textarea.id) {

            textarea.id =
                'ck_content_' +
                Date.now() +
                '_' +
                Math.random()
                    .toString(36)
                    .substring(2, 8);
        }

        console.log(
            'Initializing CKEditor:',
            textarea.id
        );

        // Already initialized hai to skip
        if (CKEDITOR.instances[textarea.id]) {

            console.log(
                'Already initialized:',
                textarea.id
            );

            return;
        }

        CKEDITOR.replace(textarea.id, {

            filebrowserUploadUrl: '{{ url("base/uploder") }}',

            enterMode: CKEDITOR.ENTER_BR,

            on: {

                instanceReady: function (ev) {

                    console.log(
                        'CKEditor READY:',
                        textarea.id
                    );

                    ev.editor.setData(textarea.value);

                }

            }

        });

    });
}

    function loadCkeditorScript(callback) {

        if (typeof CKEDITOR !== 'undefined') {
            callback();
            return;
        }

        let existingScript =document.querySelector('script[data-ckeditor-script="true"]');
        if (existingScript) {
            existingScript.addEventListener(
                'load',
                function () {callback();}
            );

            return;
        }

        var script = document.createElement('script');
        script.src = '{{ asset("assets/js/ckeditor/ckeditor.js") }}';

        script.setAttribute('data-ckeditor-script','true');
        script.onload = function () {
            console.log('CKEditor script loaded');
            callback();
        };

        script.onerror = function () {
            console.error('Unable to load CKEditor script.' );
        };

        document.head.appendChild(script);
    }

    function calculateSellingModifyPrice(row) {
        const price = parseFloat(row.find('.v_input_price').val()) || 0;
        const discount = parseFloat(row.find('.discount_popup').val()) || 0;
        const discountType = row.find('.discount_type_popup').val();

        let sellingPrice = 0;

        if (discountType === 'flat') {
            sellingPrice = price - discount;
        } else if (discountType === 'percentage') {
            sellingPrice = price - ((price * discount) / 100);
        } else {
            sellingPrice = price;
        }

        sellingPrice = Math.max(sellingPrice, 0);
        row.find('.v_input_sale_price').val(sellingPrice.toFixed(2));
}

$(document).on('input change', '.v_input_price, .discount_popup, .discount_type_popup', function () {
    const row = $(this).closest('tr');
    calculateSellingModifyPrice(row);
});

// $(document).on('input change', '.v_input_price, .discount_popup, .discount_type_popup', function () {
//     const row = $(this).closest('tr');
//     calculateSellingModifyPrice(row);
// });


$(document).on('blur', 'input[type="number"]', function () {
    let val = parseFloat($(this).val());
    if (val < 0 || isNaN(val)) {
        $(this).val(0);
    }
});

$(document).on('keydown', 'input[type="number"]', function (e) {
    // Block "-" key
    if (e.key === '-' || e.keyCode === 189) {
        e.preventDefault();
    }
});

</script>

<script>
    document.querySelectorAll('.out-of-stock-toggle').forEach(toggle => {
        toggle.addEventListener('change', function (e) {

            const checkbox = e.target;
            const isOutOfStock = e.target.checked ? 1 : 0;
            const variantId = e.target.dataset.variantId;
            const productId = e.target.dataset.productId;

            if(isOutOfStock==1){
                $('.input_quantity_'+variantId).val('0');
            }

            const message = checkbox.checked
                ? 'Are you sure you want to mark this variant as OUT OF STOCK?'
                : 'Are you sure you want to mark this variant as IN STOCK?';

            if (!confirm(message)) {
                checkbox.checked = !checkbox.checked; // revert
                return;
            }

            fetch("{{ route('admin-product-variant.stock.toggle') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    variant_id: variantId,
                    product_id: productId,
                    is_out_of_stock: isOutOfStock
                })
            });
        });
    });
  
</script>

<script>
$(document).on('click', '.update-variant-btn', function (e) {
    e.preventDefault();
    let primaryId = $(this).data('primary-id');
    let productId = $(this).data('product-id');

    let rows = $('#variant_body_' + primaryId + ' tr');
    let variants = [];

    rows.each(function () {
        let row = $(this);

        variants.push({
            combo: row.find('input[name="combo[]"]').val(),
            variant_id: row.find('input[name="variant_id[]"]').val(),
            variant_name: row.find('input[name="variant_name[]"]').val(),
            sku: row.find('input[name="variant_sku[]"]').val(),
            price: row.find('input[name="variant_price[]"]').val(),
            discount_type: row.find('select[name="variant_discount_type[]"]').val(),
            discount: row.find('input[name="variant_discount[]"]').val(),
            selling_price: row.find('input[name="variant_sale_price[]"]').val(),
            qty: row.find('input[name="variant_qty[]"]').val(),
        });
    });
    if (typeof CKEDITOR !== 'undefined') {
        Object.keys(CKEDITOR.instances).forEach(function (instance) {
            CKEDITOR.instances[instance].updateElement();
        });
    }
    let specification = $('#specialization_' + primaryId).val();
    $.ajax({
        url: "{{ route('admin-product-product.prices.update') }}",
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            product_id: productId,
            primary_variant_id: primaryId,
            variants: variants,
            specification:specification,

        },
        beforeSend: function () {
            $('.update-variant-btn').prop('disabled', true).text('Saving...');
        },
        success: function (res) {
            $('.update-variant-btn').prop('disabled', false).text('Update Variants');
            alert('Variants updated successfully');
        },
        error: function (xhr) {
            $('.update-variant-btn').prop('disabled', false).text('Update Variants');
            alert('Something went wrong');
        }
    });
});
</script>


<style>
/* Table base */
.variant-combo-table th,
.variant-combo-table td {
    vertical-align: middle;
    padding: 0.75rem;
    font-size: 14px;
}

/* Group styling */
.variant_group_row {
    border: 1px solid #dee2e6;
    border-radius: 8px;
}

.variant_group_row .card-header {
    background-color: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6;
    padding: 0.75rem 1rem;
}

/* Input styles */
.variant-combo-table input[type="text"],
.variant-combo-table input[type="number"],
.variant-combo-table select {
    border-radius: 0.375rem;
    border: 1px solid #ced4da;
    padding: 0.45rem 0.75rem;
    font-size: 14px;
    height: 38px;
    box-shadow: none;
    transition: all 0.2s ease-in-out;
}

.variant-combo-table input[type="text"]:focus,
.variant-combo-table input[type="number"]:focus,
.variant-combo-table select:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.25);
}

/* Input group prefix */
.variant-combo-table .input-group-text.dynamic-sku-prefix {
    background-color: #f1f3f5;
    border-radius: 0.375rem 0 0 0.375rem;
    border: 1px solid #ced4da;
    padding: 0.45rem 0.75rem;
    font-size: 14px;
}

/* Adjust input next to prefix */
.variant-combo-table .input-group input {
    border-radius: 0 0.375rem 0.375rem 0;
}

/* Actions column */
.variant-combo-table td a.text-danger {
    font-size: 18px;
    transition: 0.2s;
}

.variant-combo-table td a.text-danger:hover {
    color: #dc3545;
    transform: scale(1.1);
}

/* Select dropdown appearance */
.variant-combo-table select {
    background-color: #fff;
    background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%23666' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1em;
    appearance: none;
    padding-right: 2.5rem;
}
</style>
