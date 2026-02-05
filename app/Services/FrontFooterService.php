<?php

namespace App\Services;

class FrontFooterService
{
    /**
     * Get footer columns (title + links) from config.
     *
     * @return array<int, array{id: string, title: string, links: array<int, array{label: string, url: string}>}>
     */
    public function getColumns(): array
    {
        $columns = config('front.footer.columns');

        $this->translateColumns($columns);

        return $columns;
    }

    /**
     * @param  array<int, array{id: string, title: string, links: array}>  $columns
     */
    protected function translateColumns(array &$columns): void
    {
        foreach ($columns as &$column) {
            if (! empty($column['title'])) {
                $column['title'] = $this->translateKey('footer.' . $column['id'] . '_title', $column['title']);
            }

            if (! empty($column['links'])) {
                foreach ($column['links'] as $index => &$link) {
                    if (! empty($link['label'])) {
                        $link['label'] = $this->translateKey(
                            'footer.' . $column['id'] . '_' . $index,
                            $link['label']
                        );
                    }
                }
            }
        }
    }

    protected function translateKey(string $key, string $fallback): string
    {
        $translated = __('front/general.' . $key);

        return $translated !== 'front/general.' . $key ? $translated : $fallback;
    }
}
