@extends('layouts.managerlayout')

@section('content')
<style>
  .form-header {
    margin-bottom: 25px;
    border-bottom: 2px solid var(--background);
    padding-bottom: 15px;
  }

  .form-header h2 {
    color: var(--text-main);
    font-weight: 800;
    font-size: 1.5rem;
  }

  .form-card {
    background: var(--section-bg);
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 10px 25px var(--glass);
    border: 1px solid var(--border-color);
    max-width: 1000px;
    margin: auto;
  }

  .input-group-custom {
    margin-bottom: 20px;
  }

  .input-group-custom label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--text-main);
  }

  .custom-input {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--text-main);
    transition: all 0.3s;
    background: var(--background);
  }

  .custom-input:focus {
    border-color: var(--primary);
    outline: none;
    background: var(--background);
    box-shadow: 0 0 0 3px var(--primary-soft);
  }

  .product-table-header {
    display: grid;
    grid-template-columns: 2.5fr 1fr 1fr 1.2fr 50px;
    gap: 15px;
    padding: 10px 15px;
    background: var(--background);
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.85rem;
    color: var(--text-muted);
    text-transform: uppercase;
    margin-bottom: 10px;
  }

  .product-row {
    display: grid;
    grid-template-columns: 2.5fr 1fr 1fr 1.2fr 50px;
    gap: 15px;
    margin-bottom: 12px;
    align-items: center;
    background: var(--glass);
    padding: 10px;
    border-radius: 10px;
    border: 1px solid var(--border-color);
    transition: 0.3s;
  }

  .product-row:hover {
    border-color: var(--primary-light);
  }

  .remove-btn {
    background: var(--danger);
    color: #ef4444;
    border: 1px solid #fee2e2;
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: 0.3s;
  }

  .remove-btn:hover {
    background: #ef4444;
    color: white;
  }

  .add-btn {
    background: var(--primary-soft);
    color: var(--primary);
    padding: 10px 20px;
    border-radius: 8px;
    border: 1px dashed var(--primary);
    font-weight: 700;
    cursor: pointer;
    width: 100%;
    transition: 0.3s;
  }

  .add-btn:hover {
    background: var(--primary);
    color: white;
  }

  .summary-card {
    margin-top: 30px;
    background: var(--background);
    padding: 20px;
    border-radius: 12px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
  }

  .net-total-box {
    text-align: right;
  }

  .net-total-box span {
    font-size: 0.9rem;
    color: var(--text-muted);
    font-weight: 600;
  }

  .net-total-box h3 {
    font-size: 1.8rem;
    color: var(--primary);
    font-weight: 900;
    margin: 0;
  }

  .submit-btn {
    background: var(--primary);
    color: white;
    padding: 15px 40px;
    border-radius: 10px;
    border: none;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-left: auto;
    margin-top: 20px;
    transition: 0.3s;
  }

  .submit-btn:hover {
    background: #1e1eff;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px var(--primary-soft);
  }

  /* Mobile Responsive */
  @media(max-width: 768px) {
    .form-card {
      padding: 15px;
    }

    .product-table-header {
      display: none;
      /* মোবাইলে টেবিল হেডার হাইড থাকবে */
    }

    .product-row {
      grid-template-columns: 1fr;
      gap: 10px;
      padding: 15px;
      position: relative;
      background: var(--background);
      border: 1px solid var(--border-color);
    }

    /* মোবাইলে প্রতিটি ইনপুটকে লেবেল ছাড়াই চেনার জন্য স্টাইল */
    .product-row div:nth-child(1):before {
      content: "Product Selection";
      display: block;
      font-size: 11px;
      color: var(--primary);
      margin-bottom: 5px;
      font-weight: bold;
    }

    .product-row div:nth-child(2):before {
      content: "Rate";
      display: block;
      font-size: 11px;
      color: var(--text-muted);
      margin-bottom: 5px;
    }

    .product-row div:nth-child(3):before {
      content: "Quantity";
      display: block;
      font-size: 11px;
      color: var(--text-muted);
      margin-bottom: 5px;
    }

    .product-row div:nth-child(4):before {
      content: "Subtotal";
      display: block;
      font-size: 11px;
      color: var(--text-muted);
      margin-bottom: 5px;
    }

    .remove-btn {
      width: 100%;
      height: 40px;
      margin-top: 10px;
      background: #fee2e2;
    }

    .summary-card {
      justify-content: center;
      text-align: center;
    }

    .net-total-box h3 {
      font-size: 1.5rem;
    }

    .submit-btn {
      width: 100%;
      justify-content: center;
    }

  }
</style>

<div class="form-card">
  <div class="form-header">
    <h2><i class="fas fa-file-invoice"></i> Create Stock-in Request</h2>
    @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif
  </div>

  <form method="POST" action="{{ route('manager.stock.store') }}" id="stockForm">
    @csrf

    <div class="input-group-custom">
      <label>Select Supplier</label>
      <select name="supplier_id" class="custom-input" required>
        <option value="">--Choose a Supplier--</option>
        @foreach($suppliers as $supplier)
        <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
        @endforeach
      </select>
    </div>

    <div class="product-table-header">
      <span>Product</span>
      <span>Rate</span>
      <span>Qty</span>
      <span>Subtotal</span>
      <span></span>
    </div>

    <div id="product-wrapper">
    </div>

    <button type="button" class="add-btn" id="addMoreBtn" onclick="addRow()" disabled>
      <i class="fas fa-plus-circle"></i> Add New Product Row
    </button>

    <div class="summary-card">
      <div class="net-total-box">
        <span>Total Amount (Estimated)</span>
        <h3 id="netTotalDisplay">0.00</h3>
        <input type="hidden" name="net_total" id="netTotalInput">
      </div>
    </div>

    <button type="submit" class="submit-btn">
      Send Stock Request <i class="fas fa-paper-plane"></i>
    </button>
  </form>
</div>
@endsection

@push('scripts')
<script type="module">
  let index = 0;
    let currentSupplierProducts = []; 

    $(document).ready(function() {
        // Supplier change event
        $('select[name="supplier_id"]').on('change', function() {
            let supplierId = $(this).val();
            let productWrapper = $('#product-wrapper');
            
            if (supplierId) {
                $.ajax({
                    url: "/manager/stock/get-products/" + supplierId, 
                    type: "GET",
                    success: function(data) {
                        currentSupplierProducts = data; 
                        productWrapper.empty(); 
                        index = 0; 
                        
                        if (data.length > 0) {
                            $('#addMoreBtn').prop('disabled', false);
                            window.addRow(); // window দিয়ে কল করা হচ্ছে
                        } else {
                            $('#addMoreBtn').prop('disabled', true);
                            alert("This supplier has no products available!");
                        }
                        window.calculateNetTotal(); 
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        $('#addMoreBtn').prop('disabled', true);
                        alert("Error loading products.");
                    }
                });
            } else {
                productWrapper.empty();
                window.calculateNetTotal();
            }
        });
    });

    // --- গ্লোবাল ফাংশনসমূহ (window অবজেক্টে রাখা হয়েছে) ---

    window.addRow = function() {
    if (currentSupplierProducts.length === 0) {
        alert("Please select a supplier first!");
        return;
    }

    let options = `<option value="">-- Choose Product --</option>`;
    currentSupplierProducts.forEach(p => {
        options += `<option value="${p.id}" data-price="${p.price || 0}">${p.name}</option>`;
    });

    // ইনলাইন স্টাইল সরিয়ে শুধু ক্লাস রাখা হয়েছে
    let html = `
    <div class="product-row animate__animated animate__fadeIn">
        <div>
            <select name="products[${index}][product_id]" class="custom-input" required onchange="updateRow(this)">
                ${options}
            </select>
        </div>
        <div>
            <input type="number" class="custom-input rate" placeholder="0.00" readonly tabindex="-1">
        </div>
        <div>
            <input type="number" name="products[${index}][qty]" class="custom-input qty" placeholder="Qty" min="1" required oninput="updateRow(this)">
        </div>
        <div>
            <input type="number" class="custom-input subtotal" placeholder="0.00" readonly tabindex="-1">
        </div>
        <div>
            <button type="button" class="remove-btn" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
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