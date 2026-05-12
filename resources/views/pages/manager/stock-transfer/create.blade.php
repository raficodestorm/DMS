@extends('layouts.managerlayout')

@section('content')
<div class="container justify-center">
  <div class="form-card" style="max-width: 900px;">
    <h2><i class="fas fa-truck-moving"></i> Create Stock Transfer Request</h2>
    <p style="margin-bottom: 20px; color: #666;">Request to move stock from your branch to another branch.</p>

    @include('components.alert')

    <form method="POST" action="{{ route('manager.stock-transfer.store') }}" id="transferForm">
      @csrf

      <div class="mb-4">
        <label>Transfer To Branch</label>
        <select name="to_branch_id" class="input-form" required>
          <option value="">-- Select Destination Branch --</option>
          @foreach($branches as $branch)
          <option value="{{ $branch->id }}">{{ $branch->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="product-table-header" style="grid-template-columns: 3fr 1.5fr 1.5fr 3fr 50px;">
        <span>Product</span>
        <span>Available Stock</span>
        <span>Transfer Qty</span>
        <span>Note</span>
        <span></span>
      </div>

      <div id="product-wrapper">
        <!-- Rows will be added here -->
      </div>

      <button type="button" class="p-add-more-btn" id="addMoreBtn" onclick="addRow()">
        <i class="fas fa-plus-circle"></i> Add Product
      </button>

      <div class="mb-4 mt-4">
        <label>General Note (Optional)</label>
        <textarea name="note" class="input-form" rows="3" placeholder="Additional details about this transfer..."></textarea>
      </div>

      <button type="submit" class="btn-submit">
        Submit Transfer Request <i class="fas fa-paper-plane"></i>
      </button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script type="module">
    let index = 0;
    const availableProducts = @json($availableProducts);

    window.addRow = function() {
        let options = `<option value="">-- Choose Product --</option>`;
        availableProducts.forEach(item => {
            options += `<option value="${item.product_id}" data-qty="${item.quantity}">${item.product.name} (Stock: ${item.quantity})</option>`;
        });

        let html = `
        <div class="product-row animate__animated animate__fadeIn" style="grid-template-columns: 3fr 1.5fr 1.5fr 3fr 50px; align-items: center; gap: 10px; margin-bottom: 10px;">
            <div>
                <select name="products[${index}][product_id]" class="input-form product-select" required onchange="updateAvailableQty(this)">
                    ${options}
                </select>
            </div>
            <div>
                <input type="number" class="input-form available-qty" placeholder="0" readonly tabindex="-1">
            </div>
            <div>
                <input type="number" name="products[${index}][quantity]" class="input-form transfer-qty" placeholder="Qty" min="1" required oninput="validateQty(this)">
            </div>
            <div>
                <input type="text" name="products[${index}][note]" class="input-form" placeholder="Item note">
            </div>
            <div>
                <button type="button" class="icon-btn delete-icon" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
            </div>
        </div>
        `;

        $('#product-wrapper').append(html);
        index++;
    };

    window.updateAvailableQty = function(el) {
        let row = $(el).closest('.product-row');
        let selectedOption = row.find('select option:selected');
        let qty = selectedOption.data('qty') || 0;
        row.find('.available-qty').val(qty);
        
        // Reset transfer qty if it exceeds new available qty
        let transferInput = row.find('.transfer-qty');
        if (parseFloat(transferInput.val()) > qty) {
            transferInput.val(qty);
        }
    };

    window.validateQty = function(el) {
        let row = $(el).closest('.product-row');
        let available = parseFloat(row.find('.available-qty').val()) || 0;
        let requested = parseFloat($(el).val()) || 0;

        if (requested > available) {
            alert('Transfer quantity cannot exceed available stock!');
            $(el).val(available);
        }
    };

    window.removeRow = function(btn) {
        if ($('.product-row').length > 1) {
            $(btn).closest('.product-row').remove();
        } else {
            alert('At least one product is required.');
        }
    };

    // Initial row
    $(document).ready(function() {
        addRow();
    });
</script>
@endpush
