import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { createSSRApp } from 'vue';
import { renderToString } from '@vue/server-renderer';
import { parse, compileScript } from '@vue/compiler-sfc';

async function component(name) {
    const url = new URL(`../../resources/js/Components/${name}.vue`, import.meta.url);
    const { descriptor } = parse(readFileSync(url, 'utf8'));
    let { content } = compileScript(descriptor, { id: name, inlineTemplate: true });
    content = content.replace(/from (['"])vue\1/g, `from ${JSON.stringify(import.meta.resolve('vue'))}`);
    content = content.replace(/from (['"])\.\/moods\1/g, `from ${JSON.stringify(new URL('./moods.js', url).href)}`);
    return (await import(`data:text/javascript;base64,${Buffer.from(content).toString('base64')}`)).default;
}

test('progress bars clamp invalid ranges and expose a label', async () => {
    const ProgressBar = await component('ProgressBar');
    for (const [value, max, expected] of [[-10, 100, 0], [150, 100, 100], [2, 0, 0], [NaN, 100, 0], [1, 4, 25]]) {
        const html = await renderToString(createSSRApp(ProgressBar, { value, max, label: 'Avancement de lecture' }));
        assert.match(html, new RegExp(`aria-valuenow="${expected}"`));
        assert.match(html, /aria-label="Avancement de lecture"/);
    }
});

test('goal fields associate errors with their input and lock during submission', async () => {
    const Fields = await component('GoalFields');
    const html = await renderToString(createSSRApp(Fields, { form: { title: '', description: '', category: '', status: 'active', processing: true, errors: { title: 'Titre obligatoire' } } }));
    assert.match(html, /<fieldset[^>]*disabled/);
    assert.match(html, /aria-describedby="title-error"/);
    assert.match(html, /id="title-error"[^>]*role="alert"/);
    assert.match(html, /maxlength="2000"/);
});

test('habit colour and weekday controls expose selection and validation', async () => {
    const Fields = await component('HabitFields');
    const html = await renderToString(createSSRApp(Fields, { form: { name: 'Sport', frequency: 'weekly', days_of_week: [1], color: '#4f46e5', processing: false, errors: { 'days_of_week.0': 'Jour dupliqué', color: 'Couleur invalide' } } }));
    assert.match(html, /aria-labelledby="weekdays-label"/);
    assert.match(html, /aria-pressed="true"/);
    assert.match(html, /Jour dupliqué/);
    assert.match(html, /Couleur invalide/);
});

test('journal content limits and mood selections remain accessible', async () => {
    const Fields = await component('JournalFields');
    const html = await renderToString(createSSRApp(Fields, { form: { date: '2026-06-18', mood: 'good', content: '', processing: false, errors: { content: 'Contenu obligatoire' } } }));
    assert.match(html, /maxlength="5000"/);
    assert.match(html, /aria-pressed="true"/);
    assert.match(html, /aria-describedby="content-error"/);
});
