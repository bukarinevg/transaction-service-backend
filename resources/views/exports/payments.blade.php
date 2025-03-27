<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Project</th>
            <th>Details</th>
            <th>Amount</th>
            <th>Currency</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($payments as $payment)
            <tr>
                <td>{{ $payment->payment_id }}</td>
                <td>{{ $payment->project->user->email ?? '' }}</td>
                <td>{{ $payment->project->name }}</td>
                <td>{{ $payment->details }}</td>
                <td>{{ $payment->amount }}</td>
                <td>{{ $payment->currency }}</td>
                <td>{{ $payment->status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
