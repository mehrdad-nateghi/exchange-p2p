<?php

namespace App\Http\Controllers;

use App\Models\ForeignBankAccount;
use App\Http\Requests\StoreForeignBankAccountRequest;
use App\Http\Requests\UpdateForeignBankAccountRequest;

class ForeignBankAccountController extends Controller
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
     * @param  \App\Http\Requests\StoreForeignBankAccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreForeignBankAccountRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ForeignBankAccount  $foreignBankAccount
     * @return \Illuminate\Http\Response
     */
    public function show(ForeignBankAccount $foreignBankAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateForeignBankAccountRequest  $request
     * @param  \App\Models\ForeignBankAccount  $foreignBankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateForeignBankAccountRequest $request, ForeignBankAccount $foreignBankAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ForeignBankAccount  $foreignBankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(ForeignBankAccount $foreignBankAccount)
    {
        //
    }
}
