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
                        <div class="member-pill">{{ $user->name ?? 'No Name' }}</div>
                    @endforeach
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
