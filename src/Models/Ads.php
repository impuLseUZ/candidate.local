<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    protected $fillable = [
        'date', 'amount', 'ad_name', 'campaign_id', 'group_id', 'impressions', 'clicks'
    ];

    public function addSingle(array $data): self
    {
        return self::updateOrCreate(
            ['ad_name' => $data['ad_name']],
            $data
        );
    }

    public function addMultiple(array $data): void
    {
        foreach ($data as $record) {
            $this->addSingle($record);
        }
    }

    public function remove(int $id): bool
    {
        return self::destroy($id) > 0;
    }

    public function updateRecord(int $id, array $data): bool
    {
        return self::where('id', $id)->update($data);
    }
}
