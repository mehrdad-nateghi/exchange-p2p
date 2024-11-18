<?php

namespace App\Http\Controllers;

use App\Models\RialBankAccount;
use App\Http\Requests\StoreRialBankAccountRequest;
use App\Http\Requests\UpdateRialBankAccountRequest;

class RialBankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRialBankAccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRialBankAccountRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RialBankAccount  $rialBankAccount
     * @return \Illuminate\Http\Response
     */
    public function show(RialBankAccount $rialBankAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRialBankAccountRequest  $request
     * @param  \App\Models\RialBankAccount  $rialBankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRialBankAccountRequest $request, RialBankAccount $rialBankAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RialBankAccount  $rialBankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(RialBankAccount $rialBankAccount)
    {
        //
    }
}
