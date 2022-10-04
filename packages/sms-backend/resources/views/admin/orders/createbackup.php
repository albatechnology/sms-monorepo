@extends('layouts.admin')
@section('content')
<div class="card">
	<div class="card-header">
		{{ trans('global.create') }} {{ trans('cruds.order.title_singular') }}
	</div>
	<div class="card-body">
		<form method="POST" action="{{ route('admin.orders.store') }}" enctype="multipart/form-data">
			@csrf
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label class="required" for="user_id">Sales</label>
						<select class="form-control {{ $errors->has('user_id') ? 'is-invalid' : '' }}" name="user_id" id="user_id" required>
							<option value="">- Select Sales -</option>
						</select>
						@if($errors->has('user_id'))
						<span class="text-danger">{{ $errors->first('user_id') }}</span>
						@endif
						<span class="help-block">{{ trans('cruds.order.fields.user_helper') }}</span>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="required" for="lead_id">Leads</label>
						<select class="form-control select2 {{ $errors->has('customer') ? 'is-invalid' : '' }}" name="lead_id" id="lead_id" required disabled>
							<option value="">- Select Customer -</option>
						</select>
						@if($errors->has('customer'))
						<span class="text-danger">{{ $errors->first('customer') }}</span>
						@endif
						<span class="help-block">{{ trans('cruds.order.fields.customer_helper') }}</span>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="required">{{ trans('cruds.order.fields.status') }}</label>
						<input type="text" name="status" class="form-control" value="{{ \App\Enums\OrderStatus::QUOTATION()->description }}" readonly>
						@if($errors->has('status'))
						<span class="text-danger">{{ $errors->first('status') }}</span>
						@endif
						<span class="help-block">{{ trans('cruds.order.fields.status_helper') }}</span>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="required">Expected Delivery Date</label>
						<input type="date" name="expected_shipping_datetime" class="form-control" required>
						@if($errors->has('expected_shipping_datetime'))
						<span class="text-danger">{{ $errors->first('expected_shipping_datetime') }}</span>
						@endif
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Note</label>
						<textarea class="form-control" rows="5" name="note">{{ old('note') }}</textarea>
						@if($errors->has('note'))
						<span class="text-danger">{{ $errors->first('note') }}</span>
						@endif
					</div>
				</div>
			</div>
			<hr>
			<button type="button" class="btn btn-primary btn-sm float-right mb-2" id="btnAddProduct"><i class="fa fa-plus"></i> Add Product</button>
			<div class="table-responsive">
				<table class="table table-hover table-bordered">
					<thead>
						<tr>
							<th width="350px">Product</th>
							<th>Price</th>
							<th>Qty</th>
							<th>Price</th>
							<th width="10"></th>
						</tr>
					</thead>
					<tbody id="containerProduct">
						<tr>
							<td>
								<select name="products[]" class="form-control selectProduct" required></select>
							</td>
							<td>
								<input type="text" class="form-control showprice" value="0" readonly>
								<input type="hidden" class="form-control price" value="0">
							</td>
							<td>
								<div class="input-group">
									<div class="input-group-prepend btnMinus">
										<span class="input-group-text">-</span>
									</div>
									<input type="text" name="qty[]" class="qty" value="1" style="width: 35px; text-align: center;" readonly>
									<div class="input-group-append btnPlus">
										<span class="input-group-text">+</span>
									</div>
								</div>
							</td>
							<td>
								<input type="text" class="form-control showsubprice" readonly>
								<input type="hidden" name="subprice" class="form-control subprice">
							</td>
							<td></td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="3">Total Price</th>
							<th colspan="2" id="totalPrice">
								<input type="hidden" name="total_price" id="inputTotalPrice">
							</th>
						</tr>
					</tfoot>
				</table>
			</div>
			<hr>
            <div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label>Discount</label>
                        <select name="discount_id" id="discount_id" class="form-control" required disabled>
							<option value="">- Select Discount -</option>
						</select>
					</div>
				</div>
            </div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label>Packing Fee</label>
						<input type="number" name="packing_fee" min="0" class="form-control formattedNumberField" value="0" id="packingFee">
						@if($errors->has('packing_fee'))
						<span class="text-danger">{{ $errors->first('packing_fee') }}</span>
						@endif
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Shipping Fee</label>
						<input type="number" name="shipping_fee" min="0" class="form-control formattedNumberField" value="0" id="shippingFee">
						@if($errors->has('shipping_fee'))
						<span class="text-danger">{{ $errors->first('shipping_fee') }}</span>
						@endif
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Additional Discount</label>
						<input type="number" name="additional_discount" min="0" class="form-control formattedNumberField" value="0" id="additional_discount">
						@if($errors->has('additional_discount'))
						<span class="text-danger">{{ $errors->first('additional_discount') }}</span>
						@endif
					</div>
				</div>
				<div class="col-md-6 d-flex">
					<button type="button" class="btn btn-info btn-sm align-self-center" id="btnCalculate"><i class="fa fa-calculator"></i> Calculate</button>
				</div>
				<div class="col-md-12">
					<div class="card card-success">
						<div class="card-body">
							<p>Price : <span id="showTotalPrice"></span></p>
							<p>Packing : <span id="showPackingFee"></span></p>
							<p>Shipping : <span id="showShippingFee"></span></p>
							<p>Additional Discount : <span id="showAdditionalDiscount"></span></p>
							<p>Sub Total : <span id="showSubTotal"></span></p>
							<input type="hidden" name="expected_price" value="" id="expected_price">
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<button class="btn btn-danger" type="submit">
					{{ trans('global.save') }}
				</button>
			</div>
		</form>
	</div>
</div>
@endsection
@push('js')
<script type="text/javascript">
	$(function () {
		function numberWithCommas(x) {
			return x.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ".");
		}

		// $(".formattedNumberField").on('keyup', function(){
		// 	if ($(this).val() == '') {
		// 		$(this).val(0);
		// 	} else {
		// 		var n = parseInt($(this).val().replace(/\D/g,''));
		// 		$(this).val(n.toLocaleString());
		// 	}
		// });

		function parseStringToInt(str){
			return parseInt(str.replace(/\./g, ''));
		}

		var totalPrice = 0;
		countTotalPrice(totalPrice);

		$('#user_id').select2({
			placeholder: 'Select sales',
			minimumInputLength: 2,
			ajax: {
				url: '{{ route("admin.orders.getsales") }}',
				dataType: 'json',
				delay: 250,
				processResults: function (data) {
					return {
						results:  $.map(data, function (item) {
							return {
								text: item.name,
								id: item.id
							}
						})
					};
				},
				cache: true
			}
		}).on('change', function(){
			$.get('{{ url("admin/orders/get-leads") }}/'+$(this).val(), function(res){
				var option = '<option value="">- Select Leads -</option>';
				$.each(res, function(id, name) {
					option += '<option value="'+id+'">'+name+'</option>';
				});
				$('#lead_id').attr('disabled', false).html(option);
			});

            var companyId = null;
            $.get('{{ url("admin/users/get-user") }}/'+$(this).val(), function(res){
                console.log('user', res)
                $.get('{{ url("admin/discounts/get-discounts") }}/'+res.company_id, function(res){
                    var option = '<option value="">- Select Discount -</option>';
                    $.each(res, function(i, discount) {
                        option += '<option value="'+discount.id+'">'+discount.name+'</option>';
                    });
                    $('#discount_id').attr('disabled', false).html(option);
                });
            });
		});

		$('#btnAddProduct').on('click', function(){
			var add = false;
			$('.selectProduct').each(function(){
				if (typeof $(this).val() === "undefined" || $(this).val() == '' || $(this).val() == null) {
					add = false;
				} else {
					add = true;
				}
			});
			var html = '';
			html += `<tr>
			<td>
			<select name="products[]" class="form-control selectProduct" required></select>
			</td>
			<td>
			<input type="text" class="form-control showprice" value="0" readonly>
			<input type="hidden" class="form-control price" value="0">
			</td>
			<td>
			<div class="input-group">
			<div class="input-group-prepend btnMinus">
			<span class="input-group-text">-</span>
			</div>
			<input type="text" name="qty[]" class="qty" value="1" style="width: 40px; text-align: center;">
			<div class="input-group-append btnPlus">
			<span class="input-group-text">+</span>
			</div>
			</div>
			</td>
			<td>
			<input type="text" name="subprice" class="form-control showsubprice" readonly>
			<input type="hidden" name="subprice" class="form-control subprice">
			</td>
			<td><button type="button" class="btn btn-danger btn-xs btnDelProduct"><i class="fa fa-trash"></i></button></td>
			</tr>`;
			if (add) {
				$('#containerProduct').append(html);
				setSelectProduct();
			}
		});

		function setSelectProduct(){
			$('.selectProduct').select2({
				placeholder: 'Select an product',
				minimumInputLength: 4,
				ajax: {
					url: '{{ route("admin.orders.getproduct") }}',
					dataType: 'json',
					delay: 250,
					processResults: function (data) {
						return {
							results:  $.map(data, function (item) {
								return {
									text: item.name,
									id: item.id
								}
							})
						};
					},
					cache: true
				}
			}).on('change', function () {
				var product = $(this);
				var changeSubPrice = product.parent().next().next().next();
				if (changeSubPrice.children('.subprice').val() > 0) {
					totalPrice -= parseInt(changeSubPrice.children('.subprice').val());
					setPrice(totalPrice);
				}
				$.get('{{ url("admin/orders/detailproductunit") }}/'+product.val(), function(price){
				console.log('new val =',price)
					product.parent().next().next().children().find('.qty').val(1);
					product.parent().next().children('.showprice').val(numberWithCommas(price));
					product.parent().next().children('.price').val(price);
					changeSubPrice.children('.showsubprice').val(numberWithCommas(price));
					changeSubPrice.children('.subprice').val(price);
					countTotalPrice(price);
				});
			});
		}
		setSelectProduct();

		function countTotalPrice(price) {
			totalPrice += parseInt(price);
			setPrice(totalPrice);
		}

		function setPrice(number){
			$('#totalPrice').text(numberWithCommas(number));
			$('#inputTotalPrice').val(number);
			// $('#totalPrice').text(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumSignificantDigits: 1 }).format(number));
		}

		$('body').on('click', '.btnPlus', function(){
			// counter($(this), 'plus');
			var btnPlus = $(this);
			var inputQty = btnPlus.siblings('.qty');
			var inputQtyValue = parseInt(inputQty.val()) + 1;
			inputQty.val(inputQtyValue);

			var price = btnPlus.parent().parent().prev().children('.price').val();
			var subPrice = parseInt(price)*inputQtyValue;
			btnPlus.parent().parent().next().children('.showsubprice').val(numberWithCommas(subPrice));
			btnPlus.parent().parent().next().children('.subprice').val(subPrice);
			totalPrice += parseInt(price);
			setPrice(totalPrice);
		});

		$('body').on('click', '.btnMinus', function(){
			// counter($(this), 'minus');
			var btnPlus = $(this);
			var inputQty = btnPlus.siblings('.qty');
			var inputQtyValue = parseInt(inputQty.val()) - 1;
			if (inputQtyValue >= 1) {
				inputQty.val(inputQtyValue);
				var price = btnPlus.parent().parent().prev().children('.price').val();
				var subPrice = parseInt(price)*inputQtyValue;
				btnPlus.parent().parent().next().children('.showsubprice').val(numberWithCommas(subPrice));
				btnPlus.parent().parent().next().children('.subprice').val(subPrice);
				totalPrice -= parseInt(price);
				setPrice(totalPrice);
			}
		});

		$('body').on('click', '.btnDelProduct', function(){
			var lastSubTotal = $(this).parent().prev().children().val();
			if (lastSubTotal > 0) {
				totalPrice -= parseInt(lastSubTotal);
				setPrice(totalPrice);
			}
			$(this).parent().parent().remove();
		});

		$('#payment_categories').on('change', function(){
			$.get('{{ url("admin/orders/get-payment-type") }}/'+$(this).val(), function(res){
				var option = '<option value="">- Select Payment Type -</option>';
				$.each(res, function(id, name){
					option += '<option value="'+id+'">'+name+'</option>';
				});
				$('#payment_type_id').attr('disabled', false).html(option);
			});
		});

		$('#btnCalculate').on('click', function(){
			var packingFee = parseStringToInt($('#packingFee').val());
			var shippingFee = parseStringToInt($('#shippingFee').val());
			var discount = parseStringToInt($('#additional_discount').val());
			console.log('packingFee', packingFee)
			console.log('shippingFee xx', shippingFee)
			console.log('shippingFee ori', $('#shippingFee').val())
			console.log('discount', discount)
			var subTotal = parseInt(totalPrice) + parseInt(packingFee) + parseInt(shippingFee) - parseInt(discount);
			console.log('subTotal', subTotal)

			$('#showTotalPrice').text(numberWithCommas(totalPrice));
			$('#showPackingFee').text(numberWithCommas(packingFee));
			$('#showShippingFee').text(numberWithCommas(shippingFee));
			$('#showAdditionalDiscount').text(numberWithCommas(discount));
			$('#showSubTotal').text(numberWithCommas(subTotal));
			$('#expected_price').val(subTotal);
		});

		$('form').on('submit', function(e){
			$('#btnCalculate').trigger('click');
		});
	});
</script>
@endpush
