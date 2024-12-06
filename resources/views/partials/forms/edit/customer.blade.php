<!-- customer -->
<div class="form-group {{ $errors->has('customer') ? ' has-error' : '' }}">
    <label for="{{ $fieldname }}" class="col-md-3 control-label">Заказчик</label>
    <div class="col-md-7 col-sm-12">
        <input class="form-control" type="text" name="{{ $fieldname }}" id="{{ $fieldname }}" value="{{ old((isset($old_val_name) ? $old_val_name : $fieldname), $item->customer) }}"{{  (Helper::checkIfRequired($item, 'customer')) ? ' required' : '' }} maxlength="191" />
        {!! $errors->first('customer', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>

