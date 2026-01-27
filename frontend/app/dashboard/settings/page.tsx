'use client';

import { useEffect, useMemo, useState } from 'react';
import { Icon } from '@iconify/react';
import { translations, type Language } from '@/lib/i18n';
import { settingsService, type UserSettingsPayload } from '@/lib/services/settingsService';

type SettingsForm = Required<UserSettingsPayload> & { reminder_times: string[] };

const DEFAULT_SETTINGS: SettingsForm = {
  theme: 'auto',
  default_focus_minutes: 25,
  pomodoro_duration: 25,
  break_minutes: 5,
  long_break_minutes: 15,
  auto_start_break: false,
  block_notifications: true,
  background_sound: false,
  daily_target_tasks: 3,
  notification_enabled: true,
  push_notifications: true,
  daily_reminders: true,
  goal_reminders: false,
  reminder_times: ['09:00', '18:00'],
  language: 'vi',
  timezone: 'Asia/Ho_Chi_Minh',
};

const clampNumber = (value: number, min: number, max: number, fallback: number) => {
  if (Number.isNaN(value)) return fallback;
  return Math.min(Math.max(value, min), max);
};

const normalizeSettings = (data: any): SettingsForm => {
  return {
    theme: data?.theme ?? DEFAULT_SETTINGS.theme,
    default_focus_minutes: data?.default_focus_minutes ?? DEFAULT_SETTINGS.default_focus_minutes,
    pomodoro_duration: data?.pomodoro_duration ?? DEFAULT_SETTINGS.pomodoro_duration,
    break_minutes: data?.break_minutes ?? DEFAULT_SETTINGS.break_minutes,
    long_break_minutes: data?.long_break_minutes ?? DEFAULT_SETTINGS.long_break_minutes,
    auto_start_break: Boolean(data?.auto_start_break ?? DEFAULT_SETTINGS.auto_start_break),
    block_notifications: Boolean(data?.block_notifications ?? DEFAULT_SETTINGS.block_notifications),
    background_sound: Boolean(data?.background_sound ?? DEFAULT_SETTINGS.background_sound),
    daily_target_tasks: data?.daily_target_tasks ?? DEFAULT_SETTINGS.daily_target_tasks,
    notification_enabled: Boolean(data?.notification_enabled ?? DEFAULT_SETTINGS.notification_enabled),
    push_notifications: Boolean(data?.push_notifications ?? DEFAULT_SETTINGS.push_notifications),
    daily_reminders: Boolean(data?.daily_reminders ?? DEFAULT_SETTINGS.daily_reminders),
    goal_reminders: Boolean(data?.goal_reminders ?? DEFAULT_SETTINGS.goal_reminders),
    reminder_times: Array.isArray(data?.reminder_times) ? data.reminder_times : DEFAULT_SETTINGS.reminder_times,
    language: data?.language ?? DEFAULT_SETTINGS.language,
    timezone: data?.timezone ?? DEFAULT_SETTINGS.timezone,
  };
};

const ToggleField = ({
  label,
  description,
  checked,
  onChange,
}: {
  label: string;
  description?: string;
  checked: boolean;
  onChange: (value: boolean) => void;
}) => (
  <div className="flex items-center justify-between gap-4 bg-white/5 border border-white/10 rounded-xl px-4 py-3">
    <div>
      <div className="text-sm text-white font-semibold">{label}</div>
      {description && <div className="text-xs text-white/60 mt-1">{description}</div>}
    </div>
    <label className="relative inline-flex items-center cursor-pointer" aria-label={label}>
      <input
        type="checkbox"
        className="sr-only peer"
        checked={checked}
        onChange={(event) => onChange(event.target.checked)}
        aria-label={label}
      />
      <div className="w-10 h-6 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:bg-[#1F6FEB] transition">
        <div className="w-5 h-5 bg-white rounded-full shadow translate-x-0.5 peer-checked:translate-x-4 transition" />
      </div>
    </label>
  </div>
);

export default function SettingsPage() {
  const [currentLang, setCurrentLang] = useState<Language>('ja');
  const [settings, setSettings] = useState<SettingsForm>(DEFAULT_SETTINGS);
  const [reminderTimesInput, setReminderTimesInput] = useState(DEFAULT_SETTINGS.reminder_times.join(', '));
  const [isLoading, setIsLoading] = useState(true);
  const [isSaving, setIsSaving] = useState(false);
  const [isResetting, setIsResetting] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const [successMessage, setSuccessMessage] = useState('');
  const t = useMemo(() => translations[currentLang], [currentLang]);

  useEffect(() => {
    const loadSettings = async () => {
      setIsLoading(true);
      setErrorMessage('');
      try {
        const response = await settingsService.getSettings();
        const normalized = normalizeSettings(response?.data);
        setSettings(normalized);
        setReminderTimesInput(normalized.reminder_times.join(', '));
        setCurrentLang(normalized.language);
      } catch (error) {
        console.error('Failed to load settings:', error);
        setErrorMessage(t.settingsLoadError);
      } finally {
        setIsLoading(false);
      }
    };

    loadSettings();
  }, []);

  const updateSetting = <K extends keyof SettingsForm>(key: K, value: SettingsForm[K]) => {
    setSettings((prev) => ({ ...prev, [key]: value }));
  };

  const handleSave = async () => {
    setIsSaving(true);
    setErrorMessage('');
    setSuccessMessage('');

    const payload: SettingsForm = {
      ...settings,
      default_focus_minutes: clampNumber(settings.default_focus_minutes, 1, 120, DEFAULT_SETTINGS.default_focus_minutes),
      pomodoro_duration: clampNumber(settings.pomodoro_duration, 1, 120, DEFAULT_SETTINGS.pomodoro_duration),
      break_minutes: clampNumber(settings.break_minutes, 1, 60, DEFAULT_SETTINGS.break_minutes),
      long_break_minutes: clampNumber(settings.long_break_minutes, 1, 60, DEFAULT_SETTINGS.long_break_minutes),
      daily_target_tasks: clampNumber(settings.daily_target_tasks, 1, 100, DEFAULT_SETTINGS.daily_target_tasks),
      reminder_times: reminderTimesInput
        .split(',')
        .map((item) => item.trim())
        .filter(Boolean),
    };

    try {
      const response = await settingsService.updateSettings(payload);
      const normalized = normalizeSettings(response?.data);
      setSettings(normalized);
      setReminderTimesInput(normalized.reminder_times.join(', '));
      setCurrentLang(normalized.language);
      if (typeof window !== 'undefined' && normalized.language) {
        localStorage.setItem('selectedLanguage', normalized.language);
        window.dispatchEvent(new Event('languageChange'));
      }
      setSuccessMessage(t.settingsSaved);
    } catch (error) {
      console.error('Failed to save settings:', error);
      setErrorMessage(t.settingsSaveError);
    } finally {
      setIsSaving(false);
    }
  };

  const handleReset = async () => {
    setIsResetting(true);
    setErrorMessage('');
    setSuccessMessage('');
    try {
      const response = await settingsService.resetSettings();
      const normalized = normalizeSettings(response?.data);
      setSettings(normalized);
      setReminderTimesInput(normalized.reminder_times.join(', '));
      setCurrentLang(normalized.language);
      setSuccessMessage(t.settingsResetSuccess);
    } catch (error) {
      console.error('Failed to reset settings:', error);
      setErrorMessage(t.settingsResetError);
    } finally {
      setIsResetting(false);
    }
  };

  return (
    <div className="p-6">
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-white drop-shadow-lg">{t.settingsTitle}</h1>
          <p className="text-white/70 mt-1">{t.settingsSubtitle}</p>
        </div>
        <div className="flex items-center gap-2">
          <button
            onClick={handleReset}
            disabled={isResetting || isLoading}
            className="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl border border-white/20 transition text-sm disabled:opacity-50"
          >
            {t.settingsReset}
          </button>
          <button
            onClick={handleSave}
            disabled={isSaving || isLoading}
            className="px-4 py-2 bg-[#1F6FEB] hover:bg-[#1E40AF] text-white rounded-xl transition text-sm disabled:opacity-50"
          >
            {isSaving ? t.saving : t.settingsSave}
          </button>
        </div>
      </div>

      {isLoading ? (
        <div className="text-white/70">{t.loading}</div>
      ) : (
        <div className="grid grid-cols-1 xl:grid-cols-2 gap-6">
          <div className="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl space-y-4">
            <div className="flex items-center gap-2 text-white font-semibold">
              <Icon icon="mdi:palette" />
              {t.settingsAppearance}
            </div>
            <div>
              <label htmlFor="settings-theme" className="text-sm text-white/70">{t.settingsTheme}</label>
              <select
                id="settings-theme"
                value={settings.theme}
                onChange={(event) => updateSetting('theme', event.target.value as SettingsForm['theme'])}
                className="w-full mt-2 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                aria-label={t.settingsTheme}
              >
                <option value="auto" className="text-black">
                  {t.themeAuto}
                </option>
                <option value="light" className="text-black">
                  {t.themeLight}
                </option>
                <option value="dark" className="text-black">
                  {t.themeDark}
                </option>
              </select>
            </div>
          </div>

          <div className="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl space-y-4">
            <div className="flex items-center gap-2 text-white font-semibold">
              <Icon icon="mdi:timer-outline" />
              {t.settingsPomodoro}
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label htmlFor="settings-focus-minutes" className="text-sm text-white/70">{t.settingsFocusMinutes}</label>
                <input
                  id="settings-focus-minutes"
                  type="number"
                  min={1}
                  max={120}
                  value={settings.default_focus_minutes}
                  onChange={(event) => updateSetting('default_focus_minutes', Number(event.target.value))}
                  className="w-full mt-2 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                  aria-label={t.settingsFocusMinutes}
                />
              </div>
              <div>
                <label htmlFor="settings-pomodoro-duration" className="text-sm text-white/70">{t.settingsPomodoroDuration}</label>
                <input
                  id="settings-pomodoro-duration"
                  type="number"
                  min={1}
                  max={120}
                  value={settings.pomodoro_duration}
                  onChange={(event) => updateSetting('pomodoro_duration', Number(event.target.value))}
                  className="w-full mt-2 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                  aria-label={t.settingsPomodoroDuration}
                />
              </div>
              <div>
                <label htmlFor="settings-break-minutes" className="text-sm text-white/70">{t.settingsBreakMinutes}</label>
                <input
                  id="settings-break-minutes"
                  type="number"
                  min={1}
                  max={60}
                  value={settings.break_minutes}
                  onChange={(event) => updateSetting('break_minutes', Number(event.target.value))}
                  className="w-full mt-2 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                  aria-label={t.settingsBreakMinutes}
                />
              </div>
              <div>
                <label htmlFor="settings-long-break-minutes" className="text-sm text-white/70">{t.settingsLongBreakMinutes}</label>
                <input
                  id="settings-long-break-minutes"
                  type="number"
                  min={1}
                  max={60}
                  value={settings.long_break_minutes}
                  onChange={(event) => updateSetting('long_break_minutes', Number(event.target.value))}
                  className="w-full mt-2 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                  aria-label={t.settingsLongBreakMinutes}
                />
              </div>
            </div>
            <div className="space-y-3">
              <ToggleField
                label={t.settingsAutoStartBreak}
                checked={settings.auto_start_break}
                onChange={(value) => updateSetting('auto_start_break', value)}
              />
              <ToggleField
                label={t.settingsBlockNotifications}
                checked={settings.block_notifications}
                onChange={(value) => updateSetting('block_notifications', value)}
              />
              <ToggleField
                label={t.settingsBackgroundSound}
                checked={settings.background_sound}
                onChange={(value) => updateSetting('background_sound', value)}
              />
            </div>
          </div>

          <div className="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl space-y-4">
            <div className="flex items-center gap-2 text-white font-semibold">
              <Icon icon="mdi:target" />
              {t.settingsGoals}
            </div>
            <div>
              <label htmlFor="settings-daily-target-tasks" className="text-sm text-white/70">{t.settingsDailyTargetTasks}</label>
              <input
                id="settings-daily-target-tasks"
                type="number"
                min={1}
                max={100}
                value={settings.daily_target_tasks}
                onChange={(event) => updateSetting('daily_target_tasks', Number(event.target.value))}
                className="w-full mt-2 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                aria-label={t.settingsDailyTargetTasks}
              />
            </div>
          </div>

          <div className="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl space-y-4">
            <div className="flex items-center gap-2 text-white font-semibold">
              <Icon icon="mdi:bell-outline" />
              {t.settingsNotifications}
            </div>
            <div className="space-y-3">
              <ToggleField
                label={t.settingsNotificationEnabled}
                checked={settings.notification_enabled}
                onChange={(value) => updateSetting('notification_enabled', value)}
              />
              <ToggleField
                label={t.settingsPushNotifications}
                checked={settings.push_notifications}
                onChange={(value) => updateSetting('push_notifications', value)}
              />
              <ToggleField
                label={t.settingsDailyReminders}
                checked={settings.daily_reminders}
                onChange={(value) => updateSetting('daily_reminders', value)}
              />
              <ToggleField
                label={t.settingsGoalReminders}
                checked={settings.goal_reminders}
                onChange={(value) => updateSetting('goal_reminders', value)}
              />
              <div>
                <label htmlFor="settings-reminder-times" className="text-sm text-white/70">{t.settingsReminderTimes}</label>
                <input
                  id="settings-reminder-times"
                  value={reminderTimesInput}
                  onChange={(event) => setReminderTimesInput(event.target.value)}
                  placeholder="09:00, 18:00"
                  className="w-full mt-2 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                  aria-label={t.settingsReminderTimes}
                />
                <p className="text-xs text-white/50 mt-1">{t.settingsReminderHint}</p>
              </div>
            </div>
          </div>

          <div className="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl space-y-4">
            <div className="flex items-center gap-2 text-white font-semibold">
              <Icon icon="mdi:translate" />
              {t.settingsLocalization}
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label htmlFor="settings-language" className="text-sm text-white/70">{t.settingsLanguage}</label>
                <select
                  id="settings-language"
                  value={settings.language}
                  onChange={(event) => {
                    const lang = event.target.value as Language;
                    updateSetting('language', lang);
                    setCurrentLang(lang);
                    if (typeof window !== 'undefined') {
                      localStorage.setItem('selectedLanguage', lang);
                      window.dispatchEvent(new Event('languageChange'));
                    }
                  }}
                  className="w-full mt-2 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                  aria-label={t.settingsLanguage}
                >
                  <option value="vi" className="text-black">
                    Tiếng Việt
                  </option>
                  <option value="en" className="text-black">
                    English
                  </option>
                  <option value="ja" className="text-black">
                    日本語
                  </option>
                </select>
              </div>
              <div>
                <label htmlFor="settings-timezone" className="text-sm text-white/70">{t.settingsTimezone}</label>
                <input
                  id="settings-timezone"
                  value={settings.timezone}
                  onChange={(event) => updateSetting('timezone', event.target.value)}
                  className="w-full mt-2 px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white"
                  aria-label={t.settingsTimezone}
                />
              </div>
            </div>
          </div>
        </div>
      )}

      {(errorMessage || successMessage) && (
        <div className="mt-6">
          {errorMessage && <div className="text-red-300 text-sm">{errorMessage}</div>}
          {successMessage && <div className="text-emerald-300 text-sm">{successMessage}</div>}
        </div>
      )}
    </div>
  );
}
