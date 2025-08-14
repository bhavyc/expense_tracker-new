<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Group Members</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            padding: 40px;
        }
        h2 {
            color: #333;
            margin-bottom: 30px;
        }
        .group {
            background-color: #fff;
            padding: 25px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.08);
        }
        .group-title {
            font-size: 20px;
            color: #4CAF50;
            margin-bottom: 15px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 5px;
        }
        .members-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .member-pill {
            background-color: #e3f2fd;
            color: #0d47a1;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 14px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .no-members {
            color: #888;
            font-style: italic;
        }

        .weekly-expenses-btn {
    display: inline-block;
    padding: 10px 20px;
    background: linear-gradient(135deg, #4CAF50, #45a049);
    color: white;
    font-weight: 500;
    border-radius: 25px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 3px 6px rgba(0,0,0,0.1);
}
.weekly-expenses-btn:hover {
    background: linear-gradient(135deg, #45a049, #3e8e41);
    transform: translateY(-2px);
    box-shadow: 0 5px 10px rgba(0,0,0,0.15);
}
.weekly-expenses-btn:active {
    transform: translateY(0);
    box-shadow: 0 3px 6px rgba(0,0,0,0.1);
}
.center-div {
    text-align: center;
}
    </style>
</head>
<body>
    <h2>Group Members</h2>

    @forelse($groups as $group)
        <div class="group">
            <div class="group-title">{{ $group->name ?? 'Unnamed Group' }}</div>

            @if($group->users->count())
                <div class="members-container">
                    @foreach($group->users as $user)
                        <div class="member-pill">
                            {{ $user->name ?? 'No Name' }} - ₹{{ $user->expenses->sum('amount') }}
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 mb-3 text-center center-div">
    <a href="{{ route('group.weekly-expenses', $group->id ?? ' ') }}" 
       class="weekly-expenses-btn">
        Weekly Expenses
    </a>
</div>
            @else
                <p class="no-members">No members in this group.</p>
            @endif
        </div>
    @empty
        <p>You are not part of any groups.</p>
    @endforelse
</body>
</html>
