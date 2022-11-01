@extends('layouts.admin')
@section('content')
<div class="card">
	<div class="card-header">
		Payment <strong>#{{ $order->invoice_number }}</strong>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-12 table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Qty</th>
							<th>Product</th>
							<th>Price</th>
							<th>Subtotal</th>
						</tr>
					</thead>
					<tbody>
						@php
						$total = 0;
						@endphp
						@foreach($order->orderOrderDetails as $o)
						@php
						$subtotal = $o->product->price*$o->quantity;
						$total += $subtotal;
						@endphp
						<tr>
							<td>{{ $o->quantity }}</td>
							<td>{{ $o->product->name }}</td>
							<td>{{ number_format($o->product->price) }}</td>
							<td>{{ number_format($subtotal) }}</td>
						</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<td colspan="3" class="text-right">Total :</td>
							<td>
								{{ number_format($total) }}
							</td>
						</tr>
						<tr>
							<td colspan="3" class="text-right">Pakcing Fee :</td>
							<td>
								{{ number_format($order->packing_fee) }}
							</td>
						</tr>
						<tr>
							<td colspan="3" class="text-right">Shipping Fee :</td>
							<td>
								{{ number_format($order->shipping_fee) }}
							</td>
						</tr>
						<tr>
							<td colspan="3" class="text-right">Additional Discount :</td>
							<td>
								{{ number_format($order->additional_discount) }}
							</td>
						</tr>
						<tr>
							<th colspan="3" class="text-right">Total Price :</th>
							<th>
								Rp. {{ number_format($order->total_price) }}
							</th>
						</tr>
					</tfoot>
				</table>
				<hr>
			</div>
		</div>
		@foreach($order->orderPayments as $payment)
		<?php
		switch ((string)$payment->status) {
			case \App\Enums\PaymentStatus::PENDING:
			$status_payment = 'PENDING';
			break;
			case \App\Enums\PaymentStatus::APPROVED:
			$status_payment = 'APPROVED';
			break;
			case \App\Enums\PaymentStatus::REJECTED:
			$status_payment = 'REJECTED';
			break;
			default:
			$status_payment = 'PENDING';
			break;
		}
		?>
		<div class="position-relative border rounded mb-2">
			<div class="badge badge-primary position-absolute top-0 start-0">Payment #{{ $payment->id }} {{ $status_payment }}</div>
			<div class="row p-2 pt-3">
				<div class="col-md-4">
					<div class="form-group">
						<label class="required">Payment Category</label>
						<input type="text" class="form-control" value="{{ $payment->payment_type->payment_category->name }}" disabled>
						@if($errors->has('payment_categories'))
						<span class="text-danger">{{ $errors->first('payment_categories') }}</span>
						@endif
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label class="required">Payment Type</label>
						<input type="text" class="form-control" value="{{ $payment->payment_type->name }}" disabled>
						@if($errors->has('payment_type_id'))
						<span class="text-danger">{{ $errors->first('payment_type_id') }}</span>
						@endif
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label class="required">Amount</label>
						<input type="text" class="form-control" value="{{ number_format($payment->amount,0,',','.') }}" disabled>
						@if($errors->has('amount'))
						<span class="text-danger">{{ $errors->first('amount') }}</span>
						@endif
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Reference</label>
						<textarea class="form-control" disabled>{{ $payment->reference }}</textarea>
						@if($errors->has('reference'))
						<span class="text-danger">{{ $errors->first('reference') }}</span>
						@endif
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="required">Upload File <small class="text-warning">Max 5 MB</small></label>
						<br>
						@forelse($payment->proof as $key => $media)
						<a href="{{ $media->getUrl() }}" target="_blank" style="display: inline-block">
							<img src="{{ $media->getUrl('thumb') }}" width="100" class="m-1">
						</a>
						@empty
						<p>No payment file</p>
						@endforelse
					</div>
				</div>
			</div>
		</div>
		@endforeach
		<form method="POST" action="" enctype="multipart/form-data">
			@csrf
			<div class="position-relative border rounded">
				<div class="badge badge-success position-absolute top-0 start-0">Add Payment</div>
				<div class="row p-2 pt-3">
					<div class="col-md-4">
						<div class="form-group">
							<label class="required">Payment Category</label>
							<select class="form-control payment_categories" name="payment_category" required>
								<option value="">- Select Payment Category -</option>
								@foreach($payment_categories as $id => $pc)
								<option value="{{ $id }}">{{ $pc }}</option>
								@endforeach
							</select>
							@if($errors->has('payment_categories'))
							<span class="text-danger">{{ $errors->first('payment_categories') }}</span>
							@endif
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="required">Payment Type</label>
							<select name="payment_type_id" class="form-control payment_type_id" required disabled>
								<option value="">- Select Payment Type -</option>
							</select>
							@if($errors->has('payment_type_id'))
							<span class="text-danger">{{ $errors->first('payment_type_id') }}</span>
							@endif
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="required">Amount</label>
							<input type="text" name="amount" value="0" min="0" class="form-control formattedNumberField">
							@if($errors->has('amount'))
							<span class="text-danger">{{ $errors->first('amount') }}</span>
							@endif
							<span class="help-block text-danger" id="invalidAmountHelper" style="display: none">Invalid amount</span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Reference</label>
							<textarea class="form-control" name="reference">{{ old('reference') }}</textarea>
							@if($errors->has('reference'))
							<span class="text-danger">{{ $errors->first('reference') }}</span>
							@endif
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="required">Upload File <small class="text-warning">Max 5 MB</small></label>
							<input type="file" name="image" class="form-control">
							@if($errors->has('image'))
							<span class="text-danger">{{ $errors->first('image') }}</span>
							@endif
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<button class="btn btn-danger btn_submit" type="submit">Confirm Payment</button>
						</div>
					</div>
				</div>
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

		$(".formattedNumberField").on('keyup', function(){
			if ($(this).val() == '') {
				$(this).val(0);
			} else {
				var n = parseInt($(this).val().replace(/\D/g,''));
				$(this).val(n.toLocaleString());
			}
			$('#invalidAmountHelper').hide();
		});

		$('.payment_categories').on('change', function(){
			var pc = $(this);
			$.get('{{ url("admin/orders/get-payment-type") }}/'+pc.val(), function(res){
				var option = '<option value="">- Select Payment Type -</option>';
				$.each(res, function(id, name){
					option += '<option value="'+id+'">'+name+'</option>';
				});
				pc.parent().parent().next().find('.payment_type_id').attr('disabled', false).html(option);
			});
		});
		$('form').on('submit', function(e){
			if ($('.formattedNumberField').val() == 'NaN') {
				e.preventDefault();
				console.log($('.formattedNumberField').val());
				$('#invalidAmountHelper').show();
			}
			$('#btn_submit').attr('disabled', true).text('Loading...');
		});
	});
</script>
@endpush
