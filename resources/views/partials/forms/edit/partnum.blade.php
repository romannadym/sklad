<!-- Partnum -->
<div class="form-group {{ $errors->has('partnum') ? ' has-error' : '' }}">
    <label for="{{ $fieldname }}" class="col-md-3 control-label">Партийный ном�ер</label>
    <div class="col-md-7 col-sm-12">
        <input class="form-control" type="text" name="{{ $fieldname }}" id="{{ $fieldname }}" value="{{ old((isset($old_val_name) ? $old_val_name : $fieldname), $item->partnum) }}"{{  (Helper::checkIfRequired($item, 'partnum')) ? ' required' : '' }} maxlength="191" />
        {!! $errors->first('partnum', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>
