<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $receipt_number
 * @property int|null $room_id
 * @property int|null $order_id
 * @property int|null $event_ticket_id
 * @property string $service_type
 * @property numeric $amount_paid
 * @property string $payment_method
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\EventTicket|null $eventTicket
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\Room|null $room
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billing whereAmountPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billing whereEventTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billing whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billing wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billing whereReceiptNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billing whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billing whereServiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billing whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Billing whereUpdatedAt($value)
 */
	class Billing extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $full_name
 * @property string|null $phone
 * @property string|null $id_card_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereIdCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUpdatedAt($value)
 */
	class Customer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $show_id
 * @property string $title
 * @property string|null $description
 * @property string $performance_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShowBooking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EventTicket> $tickets
 * @property-read int|null $tickets_count
 * @property-read \App\Models\Show|null $venue
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event wherePerformanceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereShowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUpdatedAt($value)
 */
	class Event extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $event_id
 * @property string $ticket_type
 * @property int $total_capacity
 * @property int $available_seats
 * @property numeric $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Event|null $event
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereAvailableSeats($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereTicketType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereTotalCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventTicket whereUpdatedAt($value)
 */
	class EventTicket extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $card_uid
 * @property string $security_token
 * @property int|null $room_id
 * @property int $duration_days
 * @property string|null $expires_at
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MagneticCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MagneticCard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MagneticCard query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MagneticCard whereCardUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MagneticCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MagneticCard whereDurationDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MagneticCard whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MagneticCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MagneticCard whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MagneticCard whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MagneticCard whereSecurityToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MagneticCard whereUpdatedAt($value)
 */
	class MagneticCard extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property numeric $price
 * @property string $location_type
 * @property int $is_available
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuItem whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuItem whereLocationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MenuItem whereUpdatedAt($value)
 */
	class MenuItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $restaurant_table_id
 * @property int|null $room_id
 * @property numeric $total_amount
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\RestaurantTable|null $restaurantTable
 * @property-read \App\Models\Room|null $room
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereRestaurantTableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $order_id
 * @property int $menu_item_id
 * @property int $quantity
 * @property numeric $price_at_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereMenuItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem wherePriceAtOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUpdatedAt($value)
 */
	class OrderItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $number
 * @property string $location_type
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantTable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantTable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantTable query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantTable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantTable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantTable whereLocationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantTable whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantTable whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantTable whereUpdatedAt($value)
 */
	class RestaurantTable extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $number
 * @property string $type
 * @property string $status
 * @property numeric $price_per_night
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MagneticCard> $magneticCards
 * @property-read int|null $magnetic_cards_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ShowBooking> $showBookings
 * @property-read int|null $show_bookings_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room wherePricePerNight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereUpdatedAt($value)
 */
	class Room extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $room_id
 * @property int|null $magnetic_card_id
 * @property string $action
 * @property string $scanned_at
 * @property-read \App\Models\MagneticCard|null $magneticCard
 * @property-read \App\Models\Room|null $room
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomAccessLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomAccessLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomAccessLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomAccessLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomAccessLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomAccessLog whereMagneticCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomAccessLog whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomAccessLog whereScannedAt($value)
 */
	class RoomAccessLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int $total_capacity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereTotalCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereUpdatedAt($value)
 */
	class Show extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $show_id
 * @property int $user_id
 * @property int|null $room_id
 * @property string|null $seat_number
 * @property string $ticket_type
 * @property numeric $amount_paid
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowBooking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowBooking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowBooking query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowBooking whereAmountPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowBooking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowBooking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowBooking whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowBooking whereSeatNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowBooking whereShowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowBooking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowBooking whereTicketType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowBooking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowBooking whereUserId($value)
 */
	class ShowBooking extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

