// Types para a Plataforma Editorial Plash

export type UserRole = 'admin' | 'athlete' | 'collaborator' | 'partner';

export type AdminSubRole = 'ceo' | 'socio' | 'supervisor' | 'suporte';

export type EditionStatus = 'criacao' | 'aguardando_envio' | 'entregue' | 'aprovado' | 'lancado';

export type InterviewStatus = 'aguardando' | 'aprovado' | 'reprovado';

export type ContractStatus = 'pendente' | 'assinado';

export type ProductType = 'digital' | 'impresso';

export type UploadType = 'video' | 'photo' | 'pdf' | 'zip';

export type Badge = 
  | 'participante_problematico'
  | 'colaborador_atraso'
  | 'reputacao_risco'
  | 'responsavel_proativo'
  | 'entrevista_pendente'
  | 'entrevista_aprovada'
  | 'verificado'
  | 'compromisso_editorial'
  | 'parceiro_ouro'
  | 'primeira_edicao';

export interface User {
  id: string;
  name: string;
  email: string;
  role: UserRole;
  sub_role?: AdminSubRole;
  status: 'ativo' | 'inativo';
  badges: Badge[];
  created_at: string;
  updated_at: string;
  last_login?: string;
  avatar?: string;
  bio?: string;
}

export interface Edition {
  id: string;
  name: string;
  number: number;
  periodicidade: string;
  status: EditionStatus;
  tipo: ProductType;
  formato?: string;
  created_at: string;
  updated_at: string;
  deadline?: string;
}

export interface Cover {
  id: string;
  edition_id: string;
  athlete_id: string;
  collaborator_id: string;
  tipo: string;
  status: EditionStatus;
  created_at: string;
  updated_at: string;
}

export interface Upload {
  id: string;
  user_id: string;
  edition_id?: string;
  cover_id?: string;
  tipo: UploadType;
  filename: string;
  original_name: string;
  size: number;
  status: 'pendente' | 'aprovado' | 'rejeitado';
  created_at: string;
  ip_address: string;
}

export interface Interview {
  id: string;
  user_id: string;
  edition_id?: string;
  cover_id?: string;
  title: string;
  questions: InterviewQuestion[];
  answers: InterviewAnswer[];
  status: InterviewStatus;
  created_at: string;
  updated_at: string;
}

export interface InterviewQuestion {
  id: string;
  question: string;
  order: number;
}

export interface InterviewAnswer {
  question_id: string;
  answer: string;
  status: InterviewStatus;
}

export interface Contract {
  id: string;
  user_id: string;
  edition_id?: string;
  cover_id?: string;
  tipo: string;
  content: string;
  status: ContractStatus;
  signed_at?: string;
  ip_address?: string;
  hash: string;
}

export interface DigitalProduct {
  id: string;
  edition_id: string;
  partner_id: string;
  category: string;
  quality: string;
  file_size: number;
  observations?: string;
  status: EditionStatus;
  created_at: string;
  updated_at: string;
}

export interface PrintedProduct {
  id: string;
  edition_id: string;
  partner_id: string;
  size: string;
  spine_type: string;
  weight: number;
  pages: number;
  cover_image?: string;
  observations?: string;
  status: EditionStatus;
  created_at: string;
  updated_at: string;
}

export interface TalentIndication {
  id: string;
  indicated_by: string;
  name: string;
  age: number;
  instagram: string;
  photo?: string;
  story: string;
  intended_role: string;
  status: 'pendente' | 'aprovado' | 'rejeitado';
  created_at: string;
  updated_at: string;
}

export interface Commission {
  id: string;
  collaborator_id: string;
  edition_id: string;
  percentage: number;
  amount: number;
  status: 'pendente' | 'pago';
  created_at: string;
  paid_at?: string;
}

export interface Notification {
  id: string;
  user_id: string;
  title: string;
  message: string;
  type: 'info' | 'warning' | 'error' | 'success';
  read: boolean;
  created_at: string;
}

export interface ActivityLog {
  id: string;
  user_id: string;
  action: string;
  entity_type: string;
  entity_id: string;
  details: string;
  ip_address: string;
  created_at: string;
}

export interface SystemStatus {
  database: {
    status: 'online' | 'offline' | 'error';
    tables_count: number;
    missing_tables: string[];
  };
  php: {
    version: string;
    memory_limit: string;
    max_execution_time: string;
    extensions: string[];
    missing_extensions: string[];
  };
  server: {
    software: string;
    document_root: string;
    disk_space: {
      total: string;
      used: string;
      free: string;
    };
  };
  directories: DirectoryStatus[];
  routes: RouteStatus[];
  logs: LogEntry[];
}

export interface DirectoryStatus {
  path: string;
  exists: boolean;
  readable: boolean;
  writable: boolean;
  permissions: string;
}

export interface RouteStatus {
  path: string;
  status: 'ok' | 'error' | 'redirect';
  response_code: number;
  error_message?: string;
}

export interface LogEntry {
  timestamp: string;
  level: 'error' | 'warning' | 'info';
  message: string;
  context?: any;
}