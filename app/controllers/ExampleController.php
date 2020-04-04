<?php

namespace App\Controllers;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        echo "Index";
    }

    /**
     * Get specific resource.
     *
     * @param int $id
     * @return Response
     */
    public function get($id)
    {
        echo "Get $id";
    }

    /**
     * Create a new resource in storage.
     *
     * @return Response
     */
    public function create()
    {
        echo "Create";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return Response
     */
    public function update($id)
    {
        echo "Update $id";
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param int $id
     * @return Response
     */
    public function delete($id)
    {
        echo "Delete $id";
    }
}