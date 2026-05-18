@extends('layouts.managerlayout')

@section('content')
<div class="container justify-center">
  <div class="form-card">
    <h2><i class="fas fa-edit"></i> Edit Stock-in Request</h2>

    @include('components.alert')

    <form method="POST" action="{{ route('manager.stock.in.update', $request->id) }}" id="stockForm">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label>Select Supplier</label>
        <select name="supplier_id" class="input-form" required>
          <option value="">--Choose a Supplier--</option>
          @foreach($suppliers as $supplier)
          <option value="{{ $supplier->id }}" {{ $request->supplier_id == $supplier->id ? 'selected' : '' }}>
            {{ $supplier->company_name }}
          </option>
          @endforeach
        </select>
      </div>

      <div class="product-table-header" style="grid-template-columns: 2.5fr 1fr 1fr 1fr 1.2fr 50px;">
        <span>Product</span>
        <span>Rate</span>
        <span>Qty</span>
        <span>Tree Deduction</span>
        <span>Subtotal</span>
        <span></span>
      </div>

      <div id="product-wrapper">
        @foreach($request->items as $key => $item)
        <div class="product-row animate__animated animate__fadeIn" style="grid-template-columns: 2.5fr 1fr 1fr 1fr 1.2fr 50px;">
          <div>
            <select name="products[{{ $key }}][product_id]" class="input-form" required onchange="updateRow(this)">
              <option value="{{ $item->product_id }}" data-price="{{ $item->cost_price }}" selected>
                {{ $item->product->name }}
              </option>
            </select>
          </div>
          <div>
            <input type="number" class="input-form rate" value="{{ number_format($item->cost_price, 2, '.', '') }}"
              readonly tabindex="-1">
          </div>
          <div>
            <input type="number" name="products[{{ $key }}][qty]" class="input-form qty" value="{{ $item->quantity }}"
              min="1" required oninput="updateRow(this)">
          </div>
          <div>
            <input type="number" name="products[{{ $key }}][tree_deduction]" class="input-form tree-ded"
              value="{{ number_format($item->tree_deduction ?? 0, 2, '.', '') }}" min="0" step="0.01">
          </div>
          <div>
            <input type="number" class="input-form subtotal"
              value="{{ number_format($item->cost_price * $item->quantity, 2, '.', '') }}" readonly tabindex="-1">
          </div>
          <div>
            <button type="button" class="icon-btn delete-icon" onclick="removeRow(this)"><i
                class="fas fa-trash"></i></button>
          </div>
        </div>
        @endforeach
      </div>

      <button type="button" class="p-add-more-btn" id="addMoreBtn" onclick="addRow()">
        <i class="fas fa-plus-circle"></i> Add New Product Row
      </button>

      <div class="p-summary-card">
        <div class="p-net-total-box">
          <span>Total Amount (Estimated)</span>
          <h3 id="netTotalDisplay">{{ number_format($request->net_total, 2) }}</h3>
          <input type="hidden" name="net_total" id="netTotalInput" value="{{ $request->net_total }}">
        </div>
      </div>

      <button type="submit" class="btn-submit">
        Update Stock Request <i class="fas fa-save"></i>
      </button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script type="module">
  let index = {{ count($request->items) }};
    let currentSupplierProducts = [];

    $(document).ready(function() {
      
        let initialSupplierId = $('select[name="supplier_id"]').val();
        if(initialSupplierId) {
            fetchProducts(initialSupplierId, false);
        }

        $('select[name="supplier_id"]').on('change', function() {
            fetchProducts($(this).val(), true);
        });
    });

    function fetchProducts(supplierId, shouldEmpty) {
        if (supplierId) {
            $.ajax({
                url: "/manager/stock/get-products/" + supplierId,
                type: "GET",
                success: function(data) {
                    currentSupplierProducts = data;
                    if (shouldEmpty) {
                        $('#product-wrapper').empty();
                        index = 0;
                        window.addRow();
                    }
                    $('#addMoreBtn').prop('disabled', false);
                    window.calculateNetTotal();
                }
            });
        }
    }

    window.addRow = function() {
        let options = `<option value="">-- Choose Product --</option>`;
        currentSupplierProducts.forEach(p => {
            options += `<option value="${p.id}" data-price="${p.price || 0}">${p.name}</option>`;
        });

        let html = `
        <div class="product-row animate__animated animate__fadeIn" style="grid-template-columns: 2.5fr 1fr 1fr 1fr 1.2fr 50px;">
            <div>
                <select name="products[${index}][product_id]" class="input-form" required onchange="updateRow(this)">
                    ${options}
                </select>
            </div>
            <div><input type="number" class="input-form rate" placeholder="0.00" readonly tabindex="-1"></div>
            <div><input type="number" name="products[${index}][qty]" class="input-form qty" placeholder="Qty" min="1" required oninput="updateRow(this)"></div>
            <div><input type="number" name="products[${index}][tree_deduction]" class="input-form tree-ded" placeholder="0.00" min="0" step="0.01" value="0"></div>
            <div><input type="number" class="input-form subtotal" placeholder="0.00" readonly tabindex="-1"></div>
            <div><button type="button" class="icon-btn delete-icon" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></div>
        </div>`;
        $('#product-wrapper').append(html);
        index++;
    };

    window.updateRow = function(el) {
        let row = $(el).closest('.product-row');
        let selectedOption = row.find('select option:selected');
        let price = parseFloat(selectedOption.data('price')) || 0;
        let qty = parseFloat(row.find('.qty').val()) || 0;
        row.find('.rate').val(price.toFixed(2));
        row.find('.subtotal').val((price * qty).toFixed(2));
        window.calculateNetTotal();
    };

    window.removeRow = function(btn) {
        if ($('.product-row').length > 1) {
            $(btn).closest('.product-row').remove();
            window.calculateNetTotal();
        } else {
            alert('At least one product is required.');
        }
    };

    window.calculateNetTotal = function() {
        let total = 0;
        $('.subtotal').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#netTotalDisplay').text(total.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#netTotalInput').val(total.toFixed(2));
    };
</script>
@endpush