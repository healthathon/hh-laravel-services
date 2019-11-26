<?php

namespace App\Respositories;

use App\Respositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{

    // model property on class instances
    protected $model;

    // Constructor to bind model to repo
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // Get all instances of model
    public function all()
    {
        return $this->model->all();
    }

    // create a new record in the database
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    public function insert(array $data)
    {
        return $this->model->insert($data);
    }

    // update record in the database
    public function update(array $data, $id)
    {
        $record = $this->find($id);
        return $record->update($data);
    }

    // remove record from the database
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    // show the record with the given id
    public function show($id)
    {
        return $this->model->findOrFail($id);
    }

    // Get the associated model
    public function getModel()
    {
        return $this->model;
    }

    // Set the associated model
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    // Eager load database relationships
    public function with($relations)
    {
        return $this->model->with($relations);
    }

    public function where($filed,$value,$value2="")
    {
        if($value2!='')
            return $this->model->where($filed,$value,$value2);
        else
            return $this->model->where($filed,$value);
    }

    public function whereRaw($query)
    {
        return $this->model->whereRaw($query);
    }

    public function updateOrCreate($array)
    {
        return $this->model->updateOrCreate($array);
    }
    public function select($query)
    {
        return $this->model->select($query);
    }
    public function selectRaw($query)
    {
        return $this->model->selectRaw($query);
    }

    public function whereIn($filed,$value){
        return $this->model->whereIn($filed,$value);
    }

}