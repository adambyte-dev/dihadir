@extends('errors.layout')

@section('title', 'Terlalu Banyak Permintaan (429)')
@section('code', '429')
@section('message', 'Terlalu Banyak Permintaan')
@section('description', 'Sistem mendeteksi terlalu banyak pengiriman dalam waktu singkat (rate limiting). Mohon tunggu beberapa saat sebelum mencoba kembali.')
