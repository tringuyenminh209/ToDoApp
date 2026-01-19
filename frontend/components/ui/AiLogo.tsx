import { useId } from 'react';

interface AiLogoProps {
  className?: string;
  size?: number;
  title?: string;
}

export const AiLogo: React.FC<AiLogoProps> = ({ className = '', size = 40, title }) => {
  const gradientId = useId();
  const accessibleTitle = title || 'AI Logo';
  const strokeMain = Math.max(2, Math.round(size / 18));
  const strokeSub = Math.max(2, Math.round(size / 28));

  return (
    <svg
      width={size}
      height={size}
      viewBox="0 0 100 100"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      className={className}
      role="img"
      aria-label={accessibleTitle}
    >
      <title>{accessibleTitle}</title>
      <defs>
        <linearGradient id={gradientId} x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stopColor="#3B82F6" />
          <stop offset="100%" stopColor="#06B6D4" />
        </linearGradient>
      </defs>

      <path
        d="M50 10 L84 29 L84 71 L50 90 L16 71 L16 29 Z"
        stroke={`url(#${gradientId})`}
        strokeWidth={strokeMain}
        strokeLinecap="round"
        strokeLinejoin="round"
      />

      <circle cx="50" cy="50" r="5" fill={`url(#${gradientId})`} />

      <path
        d="M50 50 L50 30"
        stroke={`url(#${gradientId})`}
        strokeWidth={strokeSub}
        strokeLinecap="round"
      />
      <path
        d="M50 50 L70 62"
        stroke={`url(#${gradientId})`}
        strokeWidth={strokeSub}
        strokeLinecap="round"
      />

      <circle cx="50" cy="30" r="3.5" fill={`url(#${gradientId})`} />
      <circle cx="70" cy="62" r="3.5" fill={`url(#${gradientId})`} />
    </svg>
  );
};