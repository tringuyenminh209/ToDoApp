// frontend/components/dashboard/CustomDropdown.tsx
'use client';

import { useState, useRef, useEffect } from 'react';
import { Icon } from '@iconify/react';

interface CustomDropdownProps {
  label: string;
  icon: string;
  options: { value: string; label: string }[];
  selectedValue: string;
  onSelect: (value: string) => void;
  currentLang: 'vi' | 'en' | 'ja';
}

export default function CustomDropdown({
  label,
  icon,
  options,
  selectedValue,
  onSelect,
  currentLang,
}: CustomDropdownProps) {
  const [isOpen, setIsOpen] = useState(false);
  const dropdownRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
        setIsOpen(false);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const selectedOption = options.find((opt) => opt.value === selectedValue) || options[0];

  return (
    <div className="relative" ref={dropdownRef}>
      <div className="flex items-center space-x-2 bg-white/15 backdrop-blur-md rounded-xl px-4 py-2.5 border border-white/20 shadow-lg">
        <Icon icon={icon} className="text-white/90" />
        <label className="text-sm text-white font-medium">{label}:</label>
        <button
          type="button"
          onClick={() => setIsOpen(!isOpen)}
          className={`flex items-center justify-between bg-white border rounded-lg px-3 py-1.5 text-sm text-gray-700 min-w-[140px] transition-all ${
            isOpen
              ? 'border-[#1F6FEB] shadow-[0_0_0_3px_rgba(31,111,235,0.1)]'
              : 'border-gray-200 hover:border-[#1F6FEB] hover:shadow-[0_0_0_3px_rgba(31,111,235,0.1)]'
          }`}
        >
          <span>{selectedOption.label}</span>
          <Icon
            icon="mdi:chevron-down"
            className={`text-xs ml-2 transition-transform ${isOpen ? 'rotate-180' : ''}`}
          />
        </button>
      </div>
      {isOpen && (
        <div className="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 overflow-hidden">
          {options.map((option) => (
            <button
              key={option.value}
              type="button"
              onClick={() => {
                onSelect(option.value);
                setIsOpen(false);
              }}
              className={`w-full px-3 py-2.5 text-left text-sm transition-colors flex items-center justify-between ${
                selectedValue === option.value
                  ? 'bg-[#1F6FEB] text-white'
                  : 'text-gray-700 hover:bg-gray-50'
              }`}
            >
              <span>{option.label}</span>
              {selectedValue === option.value && (
                <Icon icon="mdi:check" className="text-white" />
              )}
            </button>
          ))}
        </div>
      )}
    </div>
  );
}
