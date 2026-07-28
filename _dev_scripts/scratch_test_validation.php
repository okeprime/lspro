<?php
$p = \App\Models\Pengajuan::find(5);
$data = json_decode($p->data_form, true);
$request = new \Illuminate\Http\Request($data);
$formFields = \App\Models\FormField::where('form_type', 'permohonan')->orderBy('order_index')->get();
$validationRules = ['tahap' => 'required'];
foreach ($formFields as $field) {
    if ($field->type === 'file') continue;
    if ($request->has($field->name) || $request->exists($field->name)) {
        $rule = $field->is_required ? 'required|' : 'nullable|';
        $rule .= match($field->type) {
            'number' => 'numeric',
            'email'  => 'email|max:255',
            'date'   => 'date',
            default  => 'string|max:1000',
        };
        $validationRules[$field->name] = rtrim($rule, '|');
    }
}
$validator = \Illuminate\Support\Facades\Validator::make($data, $validationRules);
if ($validator->fails()) {
    echo "VALIDATION FAILED:\n";
    print_r($validator->errors()->toArray());
} else {
    echo "VALIDATION PASSED\n";
}
