<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'order_item_id' => OrderItem::factory(),
            'ticket_type_id' => TicketType::factory(),
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'code' => Ticket::generateCode(),
            'holder_name' => fake()->name(),
            'status' => 'valid',
            'checked_in_at' => null,
            'checked_in_by' => null,
        ];
    }
}
