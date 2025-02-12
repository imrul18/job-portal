<style>
    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 10px;
        text-align: left;
        border: 1px solid #dee2e6;
    }

    .table th {
        background-color: #f8f9fa;
    }

    .filter-form {
        margin-bottom: 20px;
    }

    .d-flex {
        display: flex;
    }
</style>

<div class="container">
    <h2 class="mb-4">Applications List</h2>

    <!-- Status Filter -->
    <div class="d-flex justify-content-between mb-4">
        <form method="GET" action="" class="filter-form">
            <label for="job">Job:</label>
            <select name="job" id="job" class="form-control w-25 d-inline-block">
                <option value="">All</option>
                @foreach ($jobs as $job)
                    <option value="{{ $job->id }}" {{ request('job') == $job->id ? 'selected' : '' }}>
                        {{ $job->name }}
                    </option>
                @endforeach
            </select>
            <label for="status">Status:</label>
            <select name="status" id="status" class="form-control w-25 d-inline-block">
                <option value="">All</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="primary" {{ request('status') == 'primary' ? 'selected' : '' }}>Primary Selected</option>
                <option value="interview" {{ request('status') == 'interview' ? 'selected' : '' }}>Call
                    for Interview</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Job</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Uploaded at</th>
                <th>CV File</th>
                <th>Status Update</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $application)
                <tr>
                    <td>{{ $application->id }}</td>
                    <td>{{ $application?->job?->name ?? 'N/A' }}</td>
                    <td>{{ $application->name }}</td>
                    <td>{{ $application->email }}</td>
                    <td>{{ $application->phone }}</td>
                    <td>{{ $application->created_at->format('d-M-Y H:i A') }}</td>
                    <td>
                        @if ($application->resume)
                            <a href="{{ $application->resume_url }}" target="_blank">View</a>
                        @else
                            No file
                        @endif
                    </td>

                    <td>
                        <select class="form-control status-update" data-id="{{ $application->id }}" id="status"
                            name="status"
                            style="color: {{ $application->status == 'pending' ? 'orange' : ($application->status == 'approved' ? 'green' : ($application->status == 'rejected' ? 'red' : 'black')) }}; font-weight: bold;">
                            <option value="pending" style="color: orange;"
                                {{ $application->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="primary" {{ $application->status == 'primary' ? 'selected' : '' }}>Primary
                                Selected</option>
                            <option value="interview" {{ $application->status == 'interview' ? 'selected' : '' }}>Call
                                for Interview</option>
                            <option value="approved" style="color: green;"
                                {{ $application->status == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" style="color: red;"
                                {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected</option>

                        </select>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No applications found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $data->appends(['status' => request('status')])->links() }}
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".status-update").forEach(function(select) {
            select.addEventListener("change", function() {
                let applicationId = this.getAttribute("data-id");
                let newStatus = this.value;

                fetch("{{ route('applications.update.status') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            id: applicationId,
                            status: newStatus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            select.style.color = newStatus == "pending" ? "orange" : (
                                newStatus == "approved" ? "green" : (newStatus ==
                                    "rejected" ? "red" : "black"));
                        } else {}
                    })
                    .catch(error => console.error("Error:", error));
            });
        });
    });
</script>
