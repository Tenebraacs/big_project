<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$fetchForUser = function($authId, $userId) {
    auth()->login(\App\Models\User::find($authId));
    $user = \App\Models\User::find($userId);
    
    $invitations = \App\Models\JarInvitation::with(['jar', 'invitee'])->where(function ($q) use ($user) {
        $q->where('inviter_id', auth()->id())->where('invitee_id', $user->id);
    })->orWhere(function ($q) use ($user) {
        $q->where('inviter_id', $user->id)->where('invitee_id', auth()->id());
    })->get()->map(function ($inv) {
        return [
            'id' => $inv->id,
            'jar_name' => $inv->jar ? $inv->jar->name : null,
        ];
    });
    
    return $invitations->toArray();
};

echo "For ZoliProli2 (auth=2, user=1):\n";
print_r($fetchForUser(2, 1));

echo "\nFor ZoliProli (auth=1, user=2):\n";
print_r($fetchForUser(1, 2));
