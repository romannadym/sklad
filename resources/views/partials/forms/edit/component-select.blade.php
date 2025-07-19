<!-- component -->
<div id="assigned_component" class="form-group{{ $errors->has($fieldname) ? ' has-error' : '' }}"{!!  (isset($style)) ? ' style="'.e($style).'"' : ''  !!}>
    {{ Form::label($fieldname, $translated_name, array('class' => 'col-md-3 control-label')) }}
    <div class="col-md-7">
        <select class="js-data-ajax select2" data-endpoint="components" data-placeholder="{{ trans('general.select_component') }}" aria-label="{{ $fieldname }}" name="{{ $fieldname }}" style="width: 100%" id="{{ (isset($select_id)) ? $select_id : 'assigned_component_select' }}"{{ (isset($multiple)) ? ' multiple' : '' }}{!! (!empty($component_status_type)) ? ' data-component-status-type="' . $component_status_type . '"' : '' !!}{{  ((isset($required) && ($required =='true'))) ?  ' required' : '' }}>

            @if ((!isset($unselect)) && ($component_id = old($fieldname, (isset($component) ? $component->id  : (isset($item) ? $item->{$fieldname} : '')))))
                <option value="{{ $component_id }}" selected="selected" role="option" aria-selected="true"  role="option">
                    {{ (\App\Models\Component::find($component_id)) ? \App\Models\Component::find($component_id)->present()->fullName : '' }}
                </option>
            @else
                @if(!isset($multiple))
                    <option value=""  role="option">{{ trans('general.select_component') }}</option>
                @endif
            @endif
        </select>
    </div>
    {!! $errors->first($fieldname, '<div class="col-md-8 col-md-offset-3"><span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span></div>') !!}

</div>
