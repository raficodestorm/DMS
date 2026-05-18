@extends('layouts.adminlayout')

@section('content')
<div class="container justify-center">
  <div class="form-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i class="fas fa-edit"></i> Edit Stock Cut</h2>
        <a href="{{ route('admin.stock.cut.cuts.index') }}" class="btn-submit" style="width: auto; padding: 8px 15px; background: #6c757d; text-decoration: none;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    @include('components.alert')

    <form method="POST" action="{{ route('admin.stock.cut.update', $stockCut->id) }}" id="stockForm">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label>Select Supplier</label>
        <select name="supplier_id" class="input-form" required>
          <option value="">--Choose a Supplier--</option>
          @foreach($suppliers as $supplier)
          <option value="{{ $supplier->id }}" {{ $stockCut->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->company_name }}</option>
          @endforeach
        </select>
      </div>

      <div class="product-table-header" style="grid-template-columns: 2.5fr 1fr 1fr 1fr 1.2fr 50px;">
        <span>Product</span>
        <span>Rate</span>
        <span>Qty</span>
        <span>Subtotal</span>
        <span></span>
      </div>

      <div id="product-wrapper">
          @foreach($stockCut->items as $i => $item)
          <div class="product-row animate__animated animate__fadeIn" style="grid-template-columns: 2.5fr 1fr 1fr 1fr 1.2fr 50px;">
              <div>
                  <select name="products[{{ $i }}][product_id]" class="input-form" required onchange="updateRow(this)">
                      <option value="{{ $item->product_id }}" data-price="{{ $item->price }}">{{ $item->product->name }}</option>
                  </select>
              </div>
              <div>
                  <input type="number" class="input-form rate" value="{{ number_format($item->price, 2, '.', '') }}" readonly tabindex="-1">
              </div>
              <div>
                  <input type="number" name="products[{{ $i }}][qty]" class="input-form qty" value="{{ $item->quantity }}" min="1" required oninput="updateRow(this)">
              </div>
              <div>
                  <input type="number" class="input-form subtotal" value="{{ number_format($item->price * $item->quantity, 2, '.', '') }}" readonly tabindex="-1">
              </div>
              <div>
                  <button type="button" class="icon-btn delete-icon" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
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
          <h3 id="netTotalDisplay">{{ number_format($stockCut->net_total, 2) }}</h3>
          <input type="hidden" name="net_total" id="netTotalInput" value="{{ $stockCut->net_total }}">
        </div>
      </div>

      <button type="submit" class="btn-submit">
        Update Record <i class="fas fa-save"></i>
      </button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script type="module">
  let index = {{ count($stockCut->items) }};
    let currentSupplierProducts = []; 

    $(document).ready(function() {
        // Load initial supplier products
        let initialSupplierId = $('select[name="supplier_id"]').val();
        if (initialSupplierId) {
            loadProducts(initialSupplierId);
        }

        // Supplier change event
        $('select[name="supplier_id"]').on('change', function() {
            let supplierId = $(this).val();
            let productWrapper = $('#product-wrapper');
            
            if (supplierId) {
                loadProducts(supplierId, true);
            } else {
                productWrapper.empty();
                window.calculateNetTotal();
            }
        });
    });

    function loadProducts(supplierId, clearWrapper = false) {
        $.ajax({
            url: "/admin/stock/get-products/" + supplierId, 
            type: "GET",
            success: function(data) {
                currentSupplierProducts = data; 
                if (clearWrapper) {
                    $('#product-wrapper').empty(); 
                    index = 0; 
                    if (data.length > 0) {
                        $('#addMoreBtn').prop('disabled', false);
                        window.addRow();
                    } else {
                        $('#addMoreBtn').prop('disabled', true);
                        alert("This supplier has no products available!");
                    }
                } else {
                     $('#addMoreBtn').prop('disabled', false);
                     // Update existing selects with options
                     $('.product-row select').each(function(){
                         let currentVal = $(this).val();
                         let options = `<option value="">-- Choose Product --</option>`;
                         currentSupplierProducts.forEach(p => {
                            options += `<option value="${p.id}" data-price="${p.purchase_price || 0}" ${p.id == currentVal ? 'selected' : ''}>${p.name}</option>`;
                         });
                         $(this).html(options);
                     });
                }
                window.calculateNetTotal(); 
            }
        });
    }

    window.addRow = function() {
    if (currentSupplierProducts.length === 0) {
        alert("Please select a supplier first!");
        return;
    }

    let options = `<option value="">-- Choose Product --</option>`;
    currentSupplierProducts.forEach(p => {
        options += `<option value="${p.id}" data-price="${p.purchase_price || 0}">${p.name}</option>`;
    });

    let html = `
    <div class="product-row animate__animated animate__fadeIn" style="grid-template-columns: 2.5fr 1fr 1fr 1fr 1.2fr 50px;">
        <div>
            <select name="products[${index}][product_id]" class="input-form" required onchange="updateRow(this)">
                ${options}
            </select>
        </div>
        <div>
            <input type="number" class="input-form rate" placeholder="0.00" readonly tabindex="-1">
        </div>
        <div>
            <input type="number" name="products[${index}][qty]" class="input-form qty" placeholder="Qty" min="1" required oninput="updateRow(this)">
        </div>
        <div>
            <input type="number" class="input-form subtotal" placeholder="0.00" readonly tabindex="-1">
        </div>
        <div>
            <button type="button" class="icon-btn delete-icon" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
        </div>
    </div>
    `;

    $('#product-wrapper').append(html);
    index++;
};

    window.updateRow = function(el) {
        let row = $(el).closest('.product-row');
        let selectedOption = row.find('select option:selected');
        let price = parseFloat(selectedOption.data('price')) || 0;
        let qty = parseFloat(row.find('.qty').val()) || 0;

        row.find('.rate').val(price.toFixed(2));
        let subtotal = price * qty;
        row.find('.subtotal').val(subtotal.toFixed(2));

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
            let val = parseFloat($(this).val()) || 0;
            total += val;
        });

        $('#netTotalDisplay').text(total.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#netTotalInput').val(total.toFixed(2));
    };
</script>
@endpush
