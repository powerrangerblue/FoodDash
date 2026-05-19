<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\NotificationModel;

class NotificationsController extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $customer = $this->request->customer ?? null;

        if (! $customer) {
            return $this->respond([
                'success' => false,
                'message' => 'Only customers can access notifications',
            ], 403);
        }

        if (! $this->tableExists('notifications')) {
            return $this->respond([
                'success' => true,
                'data' => [],
                'meta' => [
                    'unread_count' => 0,
                ],
            ]);
        }

        $model = new NotificationModel();
        $rows = $this->buildActiveNotificationQuery($model, (int) $customer['id'])
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll(200);

        return $this->respond([
            'success' => true,
            'data' => $rows,
            'meta' => [
                'unread_count' => $this->getUnreadCount((int) $customer['id']),
            ],
        ]);
    }

    public function unreadCount()
    {
        $customer = $this->request->customer ?? null;

        if (! $customer) {
            return $this->respond([
                'success' => false,
                'message' => 'Only customers can access notifications',
            ], 403);
        }

        if (! $this->tableExists('notifications')) {
            return $this->respond([
                'success' => true,
                'data' => [
                    'unread_count' => 0,
                ],
            ]);
        }

        return $this->respond([
            'success' => true,
            'data' => [
                'unread_count' => $this->getUnreadCount((int) $customer['id']),
            ],
        ]);
    }

    public function markAsRead($id = null)
    {
        $customer = $this->request->customer ?? null;

        if (! $customer) {
            return $this->respond([
                'success' => false,
                'message' => 'Only customers can update notifications',
            ], 403);
        }

        $notificationId = (int) $id;

        if ($notificationId <= 0) {
            return $this->respond([
                'success' => false,
                'message' => 'Invalid notification id',
            ], 400);
        }

        if (! $this->tableExists('notifications')) {
            return $this->respond([
                'success' => false,
                'message' => 'Notifications table is not available yet.',
            ], 400);
        }

        $model = new NotificationModel();
        $row = $this->findOwnedNotification($model, (int) $customer['id'], $notificationId, true);

        if (! $row) {
            return $this->respond([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        if ((int) ($row['is_read'] ?? 0) === 0) {
            $model->update($notificationId, ['is_read' => 1]);
        }

        return $this->respond([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    public function delete($id = null)
    {
        $customer = $this->request->customer ?? null;

        if (! $customer) {
            return $this->respond([
                'success' => false,
                'message' => 'Only customers can delete notifications',
            ], 403);
        }

        $notificationId = (int) $id;
        if ($notificationId <= 0) {
            return $this->respond([
                'success' => false,
                'message' => 'Invalid notification id',
            ], 400);
        }

        if (! $this->tableExists('notifications')) {
            return $this->respond([
                'success' => false,
                'message' => 'Notifications table is not available yet.',
            ], 400);
        }

        $model = new NotificationModel();
        $row = $this->findOwnedNotification($model, (int) $customer['id'], $notificationId);

        if (! $row) {
            return $this->respond([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        if ($this->fieldExists('is_deleted', 'notifications')) {
            if ((int) ($row['is_deleted'] ?? 0) === 0) {
                $model->update($notificationId, ['is_deleted' => 1]);
            }
        } else {
            $model->delete($notificationId);
        }

        return $this->respond([
            'success' => true,
            'message' => 'Notification deleted',
        ]);
    }

    public function clear()
    {
        $customer = $this->request->customer ?? null;

        if (! $customer) {
            return $this->respond([
                'success' => false,
                'message' => 'Only customers can clear notifications',
            ], 403);
        }

        if (! $this->tableExists('notifications')) {
            return $this->respond([
                'success' => false,
                'message' => 'Notifications table is not available yet.',
            ], 400);
        }

        $model = new NotificationModel();
        $builder = $this->buildActiveNotificationQuery($model, (int) $customer['id']);

        if ($this->fieldExists('is_deleted', 'notifications')) {
            $builder->set(['is_deleted' => 1])->update();
        } else {
            $builder->delete();
        }

        return $this->respond([
            'success' => true,
            'message' => 'All notifications deleted',
        ]);
    }

    protected function buildNotificationSelect(): array
    {
        $columns = [
            'id',
            sprintf('%s AS user_id', $this->notificationOwnerColumn()),
            'title',
            'message',
            'type',
        ];

        if ($this->fieldExists('order_id', 'notifications')) {
            $columns[] = 'order_id';
        } else {
            $columns[] = 'NULL AS order_id';
        }

        if ($this->fieldExists('is_read', 'notifications')) {
            $columns[] = 'is_read';
        } else {
            $columns[] = '0 AS is_read';
        }

        if ($this->fieldExists('created_at', 'notifications')) {
            $columns[] = 'created_at';
        } else {
            $columns[] = 'NULL AS created_at';
        }

        if ($this->fieldExists('updated_at', 'notifications')) {
            $columns[] = 'updated_at';
        } else {
            $columns[] = 'NULL AS updated_at';
        }

        if ($this->fieldExists('is_deleted', 'notifications')) {
            $columns[] = 'is_deleted';
        } else {
            $columns[] = '0 AS is_deleted';
        }

        return $columns;
    }

    protected function buildActiveNotificationQuery(NotificationModel $model, int $customerId): NotificationModel
    {
        $query = $model->select($this->buildNotificationSelect())
            ->where($this->notificationOwnerColumn(), $customerId);

        if ($this->fieldExists('is_deleted', 'notifications')) {
            $query->where('is_deleted', 0);
        }

        return $query;
    }

    protected function findOwnedNotification(NotificationModel $model, int $customerId, int $notificationId, bool $activeOnly = false): ?array
    {
        $query = $model->where($this->notificationOwnerColumn(), $customerId)
            ->where('id', $notificationId);

        if ($activeOnly && $this->fieldExists('is_deleted', 'notifications')) {
            $query->where('is_deleted', 0);
        }

        return $query->first();
    }

    protected function getUnreadCount(int $customerId): int
    {
        $model = new NotificationModel();
        $query = $this->buildActiveNotificationQuery($model, $customerId)
            ->where('is_read', 0);

        return (int) $query->countAllResults();
    }

    protected function notificationOwnerColumn(): string
    {
        if ($this->fieldExists('user_id', 'notifications')) {
            return 'user_id';
        }

        if ($this->fieldExists('customer_id', 'notifications')) {
            return 'customer_id';
        }

        return 'user_id';
    }

    protected function fieldExists(string $field, string $table): bool
    {
        try {
            return db_connect()->fieldExists($field, $table);
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function tableExists(string $table): bool
    {
        try {
            return db_connect()->tableExists($table);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
