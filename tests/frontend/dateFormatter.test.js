import test from 'node:test';
import assert from 'node:assert/strict';
import { spawnSync } from 'node:child_process';
import { parseCalendarDate, useDateFormatter } from '../../resources/js/Composables/useDateFormatter.js';

test('civil dates stay on the selected day in several timezones', () => {
    const moduleURL = new URL('../../resources/js/Composables/useDateFormatter.js', import.meta.url).href;
    for (const zone of ['UTC', 'America/Los_Angeles', 'Indian/Antananarivo', 'Pacific/Kiritimati']) {
        const code = `import { parseCalendarDate } from ${JSON.stringify(moduleURL)}; const d = parseCalendarDate('2026-06-18'); console.log(d.getFullYear(), d.getMonth() + 1, d.getDate());`;
        const result = spawnSync(process.execPath, ['--input-type=module', '-e', code], { env: { ...process.env, TZ: zone }, encoding: 'utf8' });
        assert.equal(result.status, 0, result.stderr);
        assert.equal(result.stdout.trim(), '2026 6 18', zone);
    }
});

test('invalid or absent dates are safe to display', () => {
    const format = useDateFormatter();
    for (const value of ['', null, 'not-a-date', '2026-02-30', '2026-13-01']) {
        assert.equal(parseCalendarDate(value), null);
        assert.equal(format.formatDate(value), '');
        assert.equal(format.isPast(value), false);
    }
    assert.equal(parseCalendarDate('2024-02-29').getDate(), 29);
});

test('relative labels distinguish future dates and calendar-day boundaries', (t) => {
    t.mock.timers.enable({ apis: ['Date'], now: new Date('2026-06-18T12:00:00') });
    const format = useDateFormatter();
    assert.equal(format.formatRelativeTime('2026-06-18'), "Aujourd'hui");
    assert.equal(format.formatRelativeTime('2026-06-17'), 'Hier');
    assert.equal(format.formatRelativeTime('2026-06-19'), 'Demain');
    assert.equal(format.formatRelativeTime('2026-06-21'), 'Dans 3 jours');
    assert.equal(format.formatRelativeTime('2026-06-15'), 'Il y a 3 jours');
    assert.equal(format.isToday('2026-06-18'), true);
    assert.equal(format.isPast('2026-06-17'), true);
});

test('explicit timestamp offsets retain their instant semantics', () => {
    assert.equal(parseCalendarDate('2026-06-18T12:00:00+03:00').getTime(), Date.parse('2026-06-18T09:00:00Z'));
});
