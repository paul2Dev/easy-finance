<div class="p-6 bg-white shadow rounded-lg">
    <h3 class="text-lg font-medium mb-4">{{ $this->getHeading() }}</h3>

    <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Instrument</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Invested</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">DCA Price</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actual Price</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Units</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($instruments as $instrument)
                <tr>
                    <td class="px-4 py-2">{{ $instrument['name'] }}</td>
                    <td class="px-4 py-2">${{ number_format($instrument['totalInvested'], 2) }}</td>
                    <td class="px-4 py-2">${{ number_format($instrument['dcaPrice'], 2) }}</td>
                    <td class="px-4 py-2">${{ number_format($instrument['actualPrice'], 2) }}</td>
                    <td class="px-4 py-2">{{ $instrument['totalUnits'] }}</td>
                    <td class="px-4 py-2">${{ number_format($instrument['profit'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
