<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class OrderNoteController extends Controller
{
    /**
     * Store Note
     */
    public function store(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $request->validate([
            'note' => 'required|string|max:2000',
            'type' => 'required|in:internal,customer,system',
            'is_pinned' => 'nullable|boolean',
            'tagged_user_id' => 'nullable|exists:users,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('order-notes', 'public');
                $attachments[] = $path;
            }
        }

        $note = OrderNote::create([
            'order_id' => $order->id,
            'type' => $request->type,
            'note' => $request->note,
            'is_pinned' => $request->boolean('is_pinned', false),
            'attachments' => $attachments,
            'created_by' => Auth::id(),
            'tagged_user_id' => $request->tagged_user_id,
        ]);

        // 添加时间线
        $order->addTimeline(
            'note_added',
            'Thêm ghi chú',
            'Đã thêm ghi chú mới',
            null,
            null,
            ['note_id' => $note->id, 'type' => $request->type]
        );

        // 刷新订单关系以确保新备注被加载
        $order->load('notes.creator', 'notes.taggedUser');

        return redirect()->route('admin.orders.show', ['id' => $order->id, 'tab' => 'notes'])
            ->with('success', 'Thêm ghi chú thành công!');
    }

    /**
     * Update Note
     */
    public function update(Request $request, $orderId, $noteId)
    {
        $order = Order::findOrFail($orderId);
        $note = OrderNote::where('order_id', $orderId)->findOrFail($noteId);

        $request->validate([
            'note' => 'required|string|max:2000',
            'type' => 'required|in:internal,customer,system',
            'is_pinned' => 'nullable|boolean',
            'tagged_user_id' => 'nullable|exists:users,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $oldNote = $note->note;
        $oldType = $note->type;
        $oldIsPinned = $note->is_pinned;

        // 处理新附件
        $attachments = $note->attachments ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('order-notes', 'public');
                $attachments[] = $path;
            }
        }

        // 更新备注
        $note->update([
            'type' => $request->type,
            'note' => $request->note,
            'is_pinned' => $request->boolean('is_pinned', false),
            'attachments' => $attachments,
            'tagged_user_id' => $request->tagged_user_id,
        ]);

        // 记录变更到时间线
        $changes = [];
        if ($oldNote !== $note->note) {
            $changes[] = "Nội dung đã thay đổi";
        }
        if ($oldType !== $note->type) {
            $typeLabels = ['internal' => 'Nội bộ', 'customer' => 'Khách hàng', 'system' => 'Hệ thống'];
            $changes[] = "Loại ghi chú: {$typeLabels[$oldType]} → {$typeLabels[$note->type]}";
        }
        if ($oldIsPinned !== $note->is_pinned) {
            $changes[] = $note->is_pinned ? "Đã ghim ghi chú" : "Đã bỏ ghim ghi chú";
        }

        $description = !empty($changes) ? implode('. ', $changes) : 'Đã cập nhật ghi chú';

        $order->addTimeline(
            'note_updated',
            'Cập nhật ghi chú',
            $description,
            $oldNote,
            $note->note,
            ['note_id' => $note->id, 'type' => $note->type]
        );

        return redirect()->route('admin.orders.show', ['id' => $order->id, 'tab' => 'notes'])
            ->with('success', 'Cập nhật ghi chú thành công!');
    }

    /**
     * Delete Note
     */
    public function destroy($orderId, $noteId)
    {
        $order = Order::findOrFail($orderId);
        $note = OrderNote::where('order_id', $orderId)->findOrFail($noteId);

        $noteContent = $note->note;
        $noteType = $note->type;

        // 删除附件
        if ($note->attachments) {
            foreach ($note->attachments as $attachment) {
                if (Storage::disk('public')->exists($attachment)) {
                    Storage::disk('public')->delete($attachment);
                }
            }
        }

        $note->delete();

        // 记录删除到时间线
        $order->addTimeline(
            'note_deleted',
            'Xóa ghi chú',
            "Đã xóa ghi chú: " . mb_substr($noteContent, 0, 100) . (mb_strlen($noteContent) > 100 ? '...' : ''),
            $noteContent,
            null,
            ['note_id' => $noteId, 'type' => $noteType]
        );

        // 刷新订单关系
        $order->load('notes.creator', 'notes.taggedUser');

        return redirect()->route('admin.orders.show', ['id' => $order->id, 'tab' => 'notes'])
            ->with('success', 'Xóa ghi chú thành công!');
    }
}

