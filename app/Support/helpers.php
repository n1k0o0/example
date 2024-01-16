<?php

if (!function_exists('activityLog')) {
    /**
     *
     */
    function activityLog($model, $action, $name, $data = null): void
    {
        activity()
            ->causedBy(auth()->user() ?? $model)
            ->performedOn($model)
            ->withProperties(['user' => auth()?->user() ?? $model, 'data' => $data ?? $model])
            ->useLog($name)
            ->log($action);
    }
}

