 @php
                $colours = [
                    'bg-theme-1',
                    'bg-theme-2',
                    'bg-theme-3',
                    'bg-theme-4',
                    'bg-theme-5',
                    'bg-theme-6',
                    'bg-white',
                ];

                $columns = 100;
                $rows = 2;
                $blocks = [];

                for ($row = 0; $row < $rows; $row++) {
                    for ($col = 0; $col < $columns; $col++) {
                        $previousLeft = $blocks[$row][$col - 1] ?? null;
                        $previousAbove = $blocks[$row - 1][$col] ?? null;

                        $availableColours = array_filter($colours, function ($colour) use ($previousLeft, $previousAbove) {
                            return $colour !== $previousLeft && $colour !== $previousAbove;
                        });

                        $blocks[$row][$col] = $availableColours[array_rand($availableColours)];
                    }
                }
        @endphp

        <header class="flex h-[66px] overflow-hidden bg-white">

            {{-- Logo --}}
            <div class="flex h-full w-[66px] shrink-0 items-center justify-start bg-white">
                <img src="{{ asset('assets/images/logo.png') }}" class="h-[66px] w-[66px] object-contain p-0" alt="Utrechts Archief">
            </div>

            {{-- Colour blocks --}}
            <div class="grid min-w-0 flex-1 grid-flow-col grid-rows-2 auto-cols-[30px] gap-[6px] overflow-hidden">
                @for ($col = 0; $col < $columns; $col++)
                    @for ($row = 0; $row < $rows; $row++)
                        <div class="{{ $blocks[$row][$col] }} size-[30px]"></div>
                    @endfor
                @endfor
            </div>

        </header>
