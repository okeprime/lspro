$i = App\Models\Invoice::first(); if($i){ $i->status='unpaid'; $i->save(); echo "Reset done"; }
