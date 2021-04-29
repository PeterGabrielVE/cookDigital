<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_id',
        'project_id',
        'status',
        'issue_date',
        'due_date',
        'discount',
        'tax_id',
        'terms',
        'created_by',
    ];

    public static $statues = [
        'Open',
        'Not Paid',
        'Partialy Paid',
        'Paid',
        'Cancelled',
    ];

    public function project()
    {
        return $this->hasOne('App\Projects', 'id', 'project_id');
    }


    public function tax()
    {
        return $this->hasOne('App\Tax', 'id', 'tax_id');
    }

    public function items()
    {
        return $this->hasMany('App\InvoiceProduct','invoice_id','id');
    }

    public function payments()
    {
        return $this->hasMany('App\InvoicePayment', 'invoice_id', 'id');
    }

    public function getSubTotal()
    {
        $subTotal = 0;
        foreach($this->items as $product)
        {
            $subTotal += $product->price;
        }

        return $subTotal;
    }


    public function getTax()
    {
        $tax = (($this->getSubTotal() - $this->discount) * (!empty($this->tax)?$this->tax->rate:0)) / 100.00;

        return $tax;
    }

    public function getTotal()
    {
        return $this->getSubTotal() - $this->discount + $this->getTax();
    }

    public function getDue()
    {
        $due = 0;
        foreach($this->payments as $payment)
        {
            $due += $payment->amount;
        }

        return $this->getTotal() - $due;
    }

    public static function change_status($invoice_id, $status)
    {

        $invoice         = Invoice::find($invoice_id);
        $invoice->status = $status;
        $invoice->update();
    }

}
