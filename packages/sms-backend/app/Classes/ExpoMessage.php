<?php


namespace App\Classes;

use App\Models\Notification;
use App\Models\NotificationDevice;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ExpoMessage
{
    public Collection $recipients;
    public string $title;
    public string $body;
    public string $code;
    public string $link;
    public ?int $badge = null;

    public function __construct(Collection $code = null)
    {
        $this->recipients = $code ?? collect([]);
    }

    public static function create(): static
    {
        return new self();
    }

    public function setBadgeFor(User $user): self
    {
        $this->badge = Notification::query()
                ->where('notifiable_id', $user->id)
                ->count() + 1;
        return $this;
    }

    public function addRecipients(User|NotificationDevice|string ...$recipients): self
    {
        if (empty($recipients)) {
            return $this;
        }

        foreach ($recipients as $recipient) {

            if ($recipient instanceof \App\Models\User) {

                // if user, add all devices of this user
                // NotificationDevice::query()
                //     ->where('user_id', $recipient->id)
                //     ->get()
                //     ->each(function (NotificationDevice $device) {
                //         $this->recipients->push($device->code);
                //     });
                $device = NotificationDevice::where('user_id', $recipient->id)->latest()->first();

                if($device){
                    $this->recipients->push($device->code);
                }
            }

            if ($recipient instanceof NotificationDevice) {
                $this->recipients->push($recipient->code);
            }

            if (is_string($recipient)) {
                $isValid = Str::of($recipient)->startsWith('ExponentPushToken');

                if (!$isValid) {
                    throw new Exception("{$recipient} is not a valid push notification token code.");
                }

                $this->recipients->push($recipient);
            }
        }

        return $this;
    }

    public function title(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function body(string $body): static
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Code defined in notification contract
     * @param string $code
     * @return $this
     */
    public function code(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    /**
     * link mapped to code defined in contract
     * @param string $link
     * @return $this
     */
    public function link(string $link): static
    {
        $this->link = $link;
        return $this;
    }

    public function badge(int $badge): static
    {
        $this->badge = $badge;
        return $this;
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function toData(): array
    {
        $this->validate();

        $data = [
            'to'    => $this->recipients->all(),
            'title' => $this->title,
            'body'  => $this->body,
            'data'  => [
                'code' => $this->code,
                'link' => $this->link,
            ],
            'sound' => 'default',
        ];

        if (!is_null($this->badge)) {
            $data['badge'] = $this->badge;
        }

        return ['json' => $data];
    }

    /**
     * Set each recipient as a single payload data.
     * @return array[]
     * @throws Exception
     */
    public function toSingleData(): array
    {
        $this->validate();

        $allData = [];

        foreach ($this->recipients->all() as $recipient) {
            $data = [
                'to'    => $recipient,
                'title' => $this->title,
                'body'  => $this->body,
                'data'  => [
                    'code' => $this->code,
                    'link' => $this->link,
                ],
                'sound' => 'default',
            ];

            if (!is_null($this->badge)) {
                $data['badge'] = $this->badge;
            }

            $allData[] = ['json' => $data];
        }

        return $allData;
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function toClearBadge(): array
    {
        $data = [
            'to'    => $this->recipients->all(),
            'badge' => 0,
        ];

        return ['json' => $data];
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function toSingleClearBadge(): array
    {
        $allData = [];

        foreach ($this->recipients->all() as $recipient) {
            $data = [
                'to'    => $recipient,
                'badge' => 0,
            ];

            $allData[] = ['json' => $data];
        }

        return $allData;
    }

    protected function validate(): void
    {
        if ($this->recipients->isEmpty()) {
            throw new Exception('Recipient cannot be empty.');
        }

        if (!$this->title) {
            throw new Exception('Title cannot be empty.');
        }

        if (!$this->body) {
            throw new Exception('Body cannot be empty.');
        }

        if (!$this->code) {
            throw new Exception('Code cannot be empty.');
        }

        if (!$this->link) {
            throw new Exception('Link cannot be empty.');
        }
    }
}
