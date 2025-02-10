<?php namespace OnTarget\Catalog\Console;

use Illuminate\Console\Command;
use OnTarget\Catalog\Models\PropertyValue;

/**
 * ProcessDuplicatePropertyValues Command
 *
 * @link https://docs.octobercms.com/3.x/extend/console-commands.html
 */
class ProcessDuplicatePropertyValues extends Command
{
    /**
     * @var string signature for the console command.
     */
    protected $signature = 'catalog:clear_duplicates';

    /**
     * @var string description is the console command description
     */
    protected $description = 'No description provided yet...';

    /**
     * handle executes the console command.
     */
    public function handle()
    {
        $original = PropertyValue::find($this->duplicate->original_id);

        $duplicateValues = PropertyValue::where('slug', 'like', $original->slug . '%')
                                        ->where('id', '!=', $original->id)
                                        ->get();

        foreach ($duplicateValues as $duplicateValue) {
            $products = $duplicateValue->products;
            $original->products()->syncWithoutDetaching($products->pluck('id')->toArray());
            $duplicateValue->delete();
        }
    }
}
