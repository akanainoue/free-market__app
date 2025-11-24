@extends('layouts.app')

@section('content')
    <h2>メッセージ編集</h2>

    <form action="{{ route('transaction.message.update', $message->id) }}" method="POST">
        @csrf
        @method('PUT')

        <textarea name="message" rows="4" cols="50">{{ old('message', $message->message) }}</textarea>
        <br>
        <button type="submit">更新</button>
    </form>
@endsection

