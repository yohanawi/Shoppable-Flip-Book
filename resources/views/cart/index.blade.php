<x-default-layout>

    @section('title')
        Shopping Cart
    @endsection

    <div id="kt_app_content_container">
        @if ($cartItems->isEmpty())
            <div class="card">
                <div class="card-body text-center py-20">
                    <i class="fas fa-shopping-cart text-muted" style="font-size: 80px;"></i>
                    <h2 class="mt-5">Your cart is empty</h2>
                    <p class="text-muted">Start shopping to add items to your cart</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>
        @else
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Cart Items ({{ $cartItems->count() }})</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-row-bordered align-middle">
                                    <thead>
                                        <tr class="fw-bold text-muted bg-light">
                                            <th class="ps-4">Product</th>
                                            <th class="text-center">Price</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-end">Subtotal</th>
                                            <th class="text-end pe-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cartItems as $item)
                                            <tr data-cart-item="{{ $item->id }}">
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $item->product->getFeaturedImageUrl() }}"
                                                            alt="{{ $item->product->name }}" class="rounded me-3"
                                                            style="width: 60px; height: 60px; object-fit: cover;">
                                                        <div>
                                                            <a href="#"
                                                                class="text-gray-800 text-hover-primary fw-bold">
                                                                {{ $item->product->name }}
                                                            </a>
                                                            <div class="text-muted fs-7">SKU:
                                                                {{ $item->product->sku }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    ${{ number_format($item->price, 2) }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <button class="btn btn-sm btn-icon btn-light"
                                                            onclick="updateQuantity({{ $item->id }}, -1)">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number"
                                                            class="form-control form-control-sm mx-2 text-center"
                                                            id="quantity-{{ $item->id }}"
                                                            value="{{ $item->quantity }}" min="1"
                                                            max="{{ $item->product->stock_quantity }}"
                                                            style="width: 60px;"
                                                            onchange="updateQuantityDirect({{ $item->id }}, this.value)">
                                                        <button class="btn btn-sm btn-icon btn-light"
                                                            onclick="updateQuantity({{ $item->id }}, 1)">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="text-end fw-bold" id="subtotal-{{ $item->id }}">
                                                    ${{ number_format($item->getSubtotal(), 2) }}
                                                </td>
                                                <td class="text-end pe-4">
                                                    <button class="btn btn-sm btn-light-danger"
                                                        onclick="removeItem({{ $item->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Order Summary</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Subtotal:</span>
                                <span class="fw-bold" id="cart-subtotal">${{ number_format($total, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Shipping:</span>
                                <span class="fw-bold">FREE</span>
                            </div>
                            <div class="separator my-4"></div>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="fs-4 fw-bold">Total:</span>
                                <span class="fs-4 fw-bold text-primary"
                                    id="cart-total">${{ number_format($total, 2) }}</span>
                            </div>
                            <button class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-lock"></i> Proceed to Checkout
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-light-primary w-100">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                        </div>
                    </div>

                    <div class="card mt-5">
                        <div class="card-body">
                            <h5 class="mb-3">
                                <i class="fas fa-shield-alt text-success"></i> Secure Checkout
                            </h5>
                            <p class="text-muted fs-7 mb-0">Your payment information is encrypted and secure.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>


    <script>
        function updateQuantity(cartItemId, delta) {
            const input = document.getElementById(`quantity-${cartItemId}`);
            const currentQty = parseInt(input.value);
            const newQty = Math.max(1, currentQty + delta);
            const maxQty = parseInt(input.max);

            if (newQty > maxQty) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Out of Stock',
                    text: `Only ${maxQty} items available in stock.`
                });
                return;
            }

            input.value = newQty;
            updateCart(cartItemId, newQty);
        }

        function updateQuantityDirect(cartItemId, quantity) {
            const qty = parseInt(quantity);
            if (qty < 1) return;
            updateCart(cartItemId, qty);
        }

        function updateCart(cartItemId, quantity) {
            $.ajax({
                url: `/cart/${cartItemId}`,
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    quantity: quantity
                },
                success: function(response) {
                    $(`#subtotal-${cartItemId}`).text('$' + response.subtotal.toFixed(2));
                    $('#cart-subtotal').text('$' + response.total.toFixed(2));
                    $('#cart-total').text('$' + response.total.toFixed(2));
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Failed to update cart'
                    });
                }
            });
        }

        function removeItem(cartItemId) {
            Swal.fire({
                title: 'Remove Item?',
                text: 'Are you sure you want to remove this item from cart?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/cart/${cartItemId}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $(`tr[data-cart-item="${cartItemId}"]`).fadeOut(300, function() {
                                $(this).remove();

                                if (response.cart_count === 0) {
                                    location.reload();
                                } else {
                                    $('#cart-subtotal').text('$' + response.total.toFixed(2));
                                    $('#cart-total').text('$' + response.total.toFixed(2));
                                }
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'Removed',
                                text: 'Item removed from cart',
                                timer: 2000
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to remove item'
                            });
                        }
                    });
                }
            });
        }
    </script>
</x-default-layout>
